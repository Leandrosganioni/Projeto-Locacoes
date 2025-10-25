<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- Adicionado: Para acessar o usuário logado
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario']);

        // Filtra pedidos se o usuário for cliente
        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get(); // Ordenar por mais recente

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        return view('pedidos.create', [
            'clientes' => Cliente::orderBy('nome')->get(), // Ordenar clientes
            'funcionarios' => Funcionario::orderBy('nome')->get(), // Ordenar funcionários
            'produtos' => Equipamento::where('quantidade_disponivel', '>', 0)->orderBy('nome')->get() // Apenas disponíveis e ordenados
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'funcionario_id' => 'required|exists:funcionarios,id',
            'local_entrega' => 'required|string|max:255',
            'data_entrega' => 'required|date',
            'produtos' => 'required|array|min:1',
            'produtos.*' => 'exists:equipamentos,id',
            'quantidades' => 'required|array',
            'quantidades.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'cliente_id' => $request->cliente_id,
                'funcionario_id' => $request->funcionario_id,
                'local_entrega' => $request->local_entrega,
                'data_entrega' => $request->data_entrega
            ]);

            // Verifica disponibilidade
            foreach ($request->produtos as $equipamento_id) {
                $quantidade = (int)($request->quantidades[$equipamento_id] ?? 0);
                $equipamento = Equipamento::find($equipamento_id);
                $nome = $equipamento ? $equipamento->nome : "ID {$equipamento_id}";

                if (!$equipamento) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Equipamento não encontrado: {$nome}.")->withInput();
                }

                if ($equipamento->quantidade_disponivel < $quantidade) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Estoque insuficiente para {$nome}. Disponível: {$equipamento->quantidade_disponivel}.")->withInput();
                }
            }

            // Cria item e reserva estoque
            foreach ($request->produtos as $equipamento_id) {
                $quantidade  = (int)($request->quantidades[$equipamento_id] ?? 0);
                if ($quantidade <= 0) continue; // Pula se quantidade for inválida

                $equipamento = Equipamento::find($equipamento_id); // Já sabemos que existe

                $item = PedidoProduto::create([
                    'pedido_id'           => $pedido->id,
                    'equipamento_id'      => $equipamento_id,
                    'quantidade'          => $quantidade,
                    'status'              => PedidoProduto::STATUS_RESERVADO, // Garanta que PedidoProduto::STATUS_RESERVADO exista
                    'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                ]);

                if (!$item->reservar()) { // Método no Model PedidoProduto que chama Equipamento::reservar
                    DB::rollBack();
                    return redirect()->back()->with('error', "Falha ao reservar {$equipamento->nome}. Verifique o estoque novamente.")->withInput();
                }
            }

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao criar pedido: ' . $e->getMessage()); // Descomente se quiser logar o erro
            return redirect()->back()->with('error', 'Erro interno ao criar pedido. Tente novamente.')->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario', 'itens.equipamento']);

        // Se for cliente, aplica filtro para garantir que só veja o seu
        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedido = $query->find($id);

        // Verifica se o pedido foi encontrado E se pertence ao cliente (caso seja cliente)
        if (!$pedido) {
            abort(404, 'Pedido não encontrado ou não pertence a você.'); // Ou redirecionar com erro
        }

        return view('pedidos.show', compact('pedido'));
    }


    public function edit($id)
    {
        // Carrega o pedido com os itens E os equipamentos dentro dos itens
        $pedido = Pedido::with(['itens.equipamento', 'cliente', 'funcionario'])->findOrFail($id);

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => Cliente::orderBy('nome')->get(), 
            'funcionarios' => Funcionario::orderBy('nome')->get(), 
            'produtos' => Equipamento::orderBy('nome')->get()
        ]);
    }

    public function update(Request $request, $id)
    {

        
        $request->validate([
            'local_entrega'  => 'required|string|max:255', 
            'data_entrega'   => 'required|date',
            'produtos'       => 'nullable|array',
            'produtos.*'     => 'exists:equipamentos,id',
            'quantidades'    => 'required_with:produtos|array',
            'quantidades.*'  => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $pedido = Pedido::with('itens.equipamento')->findOrFail($id);


            $pedido->local_entrega  = $request->local_entrega;
            $pedido->data_entrega   = $request->data_entrega;
            $pedido->save();

            $produtosSelecionados = $request->input('produtos', []);
            $quantidadesReq       = $request->input('quantidades', []);
            $quantidades = [];
            foreach ($quantidadesReq as $eid => $qtd) {
                if (in_array($eid, $produtosSelecionados)) {
                    $quantidades[$eid] = (int)$qtd;
                }
            }

            $idsSelecionados = $produtosSelecionados; 
            $idsParaManter = [];


            foreach ($pedido->itens as $item) {
                $eid = $item->equipamento_id;

                if (in_array($eid, $idsSelecionados) && isset($quantidades[$eid])) {
                    $novaQuant = $quantidades[$eid];
                    
                    if ($item->status === PedidoProduto::STATUS_RESERVADO) {
                        if ($novaQuant > 0) {
                            if (!$item->alterarQuantidade($novaQuant)) {
                                DB::rollBack();
                                return back()->with('error', "Estoque insuficiente ou erro ao atualizar {$item->equipamento->nome}.")->withInput();
                            }
                            $idsParaManter[] = $eid;
                        } else {
                            $item->cancelar(); 
                            $item->delete();
                        }
                    } else {
                         $idsParaManter[] = $eid;
                    }

                } else {
                    if ($item->status === PedidoProduto::STATUS_RESERVADO) {
                        $item->cancelar(); 
                        $item->delete();
                    }
                }
            }


            $idsParaAdicionar = array_diff($idsSelecionados, $idsParaManter);
            foreach ($idsParaAdicionar as $eid) {
                $novaQuant = $quantidades[$eid] ?? 0;
                if ($novaQuant <= 0) continue;

                $equipamento = Equipamento::find($eid);
                if (!$equipamento) { 
                    DB::rollBack();
                    return back()->with('error', "Equipamento ID {$eid} não encontrado.")->withInput();
                }

                if ($equipamento->quantidade_disponivel < $novaQuant) {
                    DB::rollBack();
                    return back()->with('error', "Estoque insuficiente para adicionar {$equipamento->nome}. Disponível: {$equipamento->quantidade_disponivel}.")->withInput();
                }

                $novoItem = PedidoProduto::create([
                    'pedido_id'           => $pedido->id,
                    'equipamento_id'      => $eid,
                    'quantidade'          => $novaQuant,
                    'status'              => PedidoProduto::STATUS_RESERVADO,
                    'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                ]);

                if (!$novoItem->reservar()) { 
                    DB::rollBack();
                    return back()->with('error', "Falha ao reservar o novo item {$equipamento->nome}.")->withInput();
                }
            }

            DB::commit();
            return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Pedido atualizado com sucesso!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            

            return back()->with('error', 'Erro interno ao atualizar pedido: ' . $e->getMessage())->withInput();
        }
    }


    public function destroy($id)
    {
        $user = Auth::user();
        // Apenas Admin e Funcionário podem excluir
        if ($user->role === 'cliente') {
             abort(403, 'Acesso não autorizado.');
        }

        DB::beginTransaction();
        try {
            $pedido = Pedido::with('itens')->findOrFail($id);

            // Libera estoque de cada item (se estiver reservado ou em locação)
            foreach ($pedido->itens as $item) {
                 if ($item->status === PedidoProduto::STATUS_RESERVADO || $item->status === PedidoProduto::STATUS_EM_LOCACAO) {
                      $item->cancelar(); // Usa o método cancelar para liberar estoque corretamente
                 }
            }

            // A exclusão dos itens é feita em cascata pelo onDelete('cascade') na migration,

            // PedidoProduto::where('pedido_id', $pedido->id)->delete();

            $pedido->delete(); // Exclui o pedido (e itens em cascata)

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao excluir pedido: ' . $e->getMessage()); // Descomente para logar
            return redirect()->route('pedidos.index')->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }

    public function comprovante($id)
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario', 'itens.equipamento']);

        // Se for cliente, aplica filtro
        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedido = $query->find($id);

        if (!$pedido) {
            abort(404, 'Pedido não encontrado ou não pertence a você.');
        }

        return view('pedidos.comprovante', compact('pedido'));
    }

    public function grafico(Pedido $pedido) // Route Model Binding já carrega o pedido
    {
        $user = Auth::user();

        // Se for cliente, verifica se o pedido pertence a ele
        if ($user->role === 'cliente' && $pedido->cliente_id !== $user->cliente_id) {
             abort(403, 'Acesso não autorizado.');
        }

        $pedido->load('itens.equipamento');
        $seriesPorItem = [];
        foreach ($pedido->itens as $item) {
            $seriesPorItem[$item->id] = $item->breakdownDecorrido();
        }
        $agregado = $this->agruparSeries($seriesPorItem);

        $events = [];
        foreach ($pedido->itens as $item) {
            if ($item->start_at) {
                $events[] = [
                    'data'        => $item->start_at->copy()->startOfDay()->format('Y-m-d'),
                    'tipo'        => 'Adição',
                    'equipamento' => $item->equipamento->nome,
                ];
            }
            if ($item->end_at) {
                $events[] = [
                    'data'        => $item->end_at->copy()->startOfDay()->format('Y-m-d'),
                    'tipo'        => 'Finalização',
                    'equipamento' => $item->equipamento->nome,
                ];
            }
        }

        return response()->json([
            'series' => $agregado,
            'events' => $events,
        ]);
    }

    public function decorridos(Pedido $pedido) // Route Model Binding
    {
        $user = Auth::user();

        // Se for cliente, verifica se o pedido pertence a ele
        if ($user->role === 'cliente' && $pedido->cliente_id !== $user->cliente_id) {
             abort(403, 'Acesso não autorizado.');
        }

        $pedido->load('itens.equipamento');
        $series = [];
        foreach ($pedido->itens as $item) {
            $series[$item->id] = $item->breakdownDecorrido();
        }
        $agregado = $this->agruparSeries($series);

        return view('pedidos.decorridos', compact('pedido', 'series', 'agregado'));
    }

    protected function agruparSeries(array $series): array
    {
        $agrupado = [];
        foreach ($series as $itemId => $dados) {
            foreach ($dados as $linha) {
                $data = $linha['data'];
                if (!isset($agrupado[$data])) {
                    $agrupado[$data] = 0.0;
                }
                $agrupado[$data] += (float)($linha['parcela'] ?? 0);
            }
        }
        ksort($agrupado); // Ordena por data
        $acumulado = 0.0;
        $resultado = [];
        foreach ($agrupado as $data => $valor) {
            $acumulado += $valor;
            $resultado[] = [
                'data'      => $data,
                'total'     => round($valor, 2),
                'acumulado' => round($acumulado, 2),
            ];
        }
        return $resultado;
    }

    /*public function update(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'funcionario_id' => 'required|exists:funcionarios,id',
            'local_entrega' => 'required|string',
            'data_entrega' => 'required|date',
            'produtos' => 'required|array',
            'quantidades' => 'required|array',
            'quantidades.*' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Carrega o pedido com os itens e equipamentos para poder cancelar
            $pedido = Pedido::with('itens.equipamento')->findOrFail($id);
            
            foreach ($pedido->itens as $item) {
                // se o item estiver ‘em_locacao’, pode decidir bloquear update ou devolver()
                $item->cancelar(); // libera a quantidade_disponivel
            }

            // atualiza dados do pedido
            $pedido->update([
                'cliente_id' => $request->cliente_id,
                'funcionario_id' => $request->funcionario_id,
                'local_entrega' => $request->local_entrega,
                'data_entrega' => $request->data_entrega
            ]);

            // Verifica de novo o estoque
            foreach ($request->produtos as $equipamento_id) {
                $quantidade = (int)($request->quantidades[$equipamento_id] ?? 0);
                $equipamento = Equipamento::find($equipamento_id);
                if (!$equipamento || $equipamento->quantidade_disponivel < $quantidade) {
                    DB::rollBack();
                    return back()->with('error', "Estoque insuficiente para o equipamento {$equipamento->nome}.");
                }
            }

            // Deleta os itens antigos
            PedidoProduto::where('pedido_id', $pedido->id)->delete();

            // Adiciona os novos itens e reserva
            foreach ($request->produtos as $equipamento_id) {
                $quantidade  = (int)$request->quantidades[$equipamento_id];
                $equipamento = Equipamento::find($equipamento_id);

                $item = PedidoProduto::create([
                    'pedido_id'           => $pedido->id,
                    'equipamento_id'      => $equipamento_id,
                    'quantidade'          => $quantidade,
                    'status'              => PedidoProduto::STATUS_RESERVADO,
                    'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                ]);

                if (!$item->reservar()) {
                    DB::rollBack();
                    return back()->with('error', "Falha ao reservar {$equipamento->nome}.");
                }
            }

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            // Carrega com itens.equipamento para o $item->cancelar() funcionar
            $pedido = Pedido::with('itens.equipamento')->findOrFail($id);

            // libera estoque de cada item (se estiver reservado)
            foreach ($pedido->itens as $item) {
                // se estiver em_locacao, pode decidir devolver() ou impedir exclusão
                $item->cancelar();
            }

            PedidoProduto::where('pedido_id', $pedido->id)->delete();
            $pedido->delete();

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pedidos.index')->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }*/
}