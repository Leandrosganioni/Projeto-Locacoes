<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario']);

        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get();

        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        return view('pedidos.create', [
            'clientes' => Cliente::orderBy('nome')->get(),
            'funcionarios' => Funcionario::orderBy('nome')->get(),
            'produtos' => Equipamento::where('quantidade_disponivel', '>', 0)->orderBy('nome')->get()
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


            foreach ($request->produtos as $equipamento_id) {
                $quantidadeTotal = (int)($request->quantidades[$equipamento_id] ?? 0);
                if ($quantidadeTotal <= 0) continue;

                $equipamento = Equipamento::find($equipamento_id);
                $nome = $equipamento ? $equipamento->nome : "ID {$equipamento_id}";

                if (!$equipamento) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Equipamento não encontrado: {$nome}.")->withInput();
                }

                if ($equipamento->quantidade_disponivel < $quantidadeTotal) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Estoque insuficiente para {$nome}. Disponível: {$equipamento->quantidade_disponivel}.")->withInput();
                }
            }


            foreach ($request->produtos as $equipamento_id) {
                $quantidadeTotal = (int)($request->quantidades[$equipamento_id] ?? 0);
                
                $equipamento = Equipamento::find($equipamento_id);


                for ($i = 0; $i < $quantidadeTotal; $i++) {
                    $item = PedidoProduto::create([
                        'pedido_id'           => $pedido->id,
                        'equipamento_id'      => $equipamento_id,
                        'quantidade'          => 1, 
                        'status'              => PedidoProduto::STATUS_RESERVADO,
                        'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                    ]);

                    if (!$item->reservar()) {
                        DB::rollBack();
                        return redirect()->back()->with('error', "Falha ao reservar uma unidade de {$equipamento->nome}.")->withInput();
                    }
                }
            }

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro interno ao criar pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario', 'itens.equipamento']);

        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedido = $query->find($id);

        if (!$pedido) {
            abort(404, 'Pedido não encontrado ou não pertence a você.');
        }

        return view('pedidos.show', compact('pedido'));
    }

    public function edit($id)
    {
        $pedido = Pedido::with('itens')->findOrFail($id);


        $quantidadesAgrupadas = [];
        $produtosSelecionados = [];

        foreach ($pedido->itens as $item) {
            if ($item->status === PedidoProduto::STATUS_CANCELADO) continue;
            
            $eid = $item->equipamento_id;
            if (!isset($quantidadesAgrupadas[$eid])) {
                $quantidadesAgrupadas[$eid] = 0;
                $produtosSelecionados[] = $eid;
            }
            $quantidadesAgrupadas[$eid] += $item->quantidade; 
        }


        $pedido->produtos_selecionados_ids = $produtosSelecionados;
        $pedido->quantidades_agrupadas = $quantidadesAgrupadas;

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

            $produtosForm = $request->input('produtos', []);
            $quantidadesForm = $request->input('quantidades', []);


            $itensNoBanco = $pedido->itens->where('status', '!=', PedidoProduto::STATUS_CANCELADO)->groupBy('equipamento_id');


            $equipamentosProcessados = [];

            foreach ($produtosForm as $eid) {
                $equipamentosProcessados[] = $eid;
                
                $qtdDesejada = (int)($quantidadesForm[$eid] ?? 0);
                if ($qtdDesejada <= 0) continue;


                $itensDesteEquipamento = $itensNoBanco->get($eid, collect());
                

                $qtdAtual = $itensDesteEquipamento->sum('quantidade');

                if ($qtdDesejada > $qtdAtual) {

                    $falta = $qtdDesejada - $qtdAtual;
                    $equipamento = Equipamento::find($eid);

                    if (!$equipamento) {
                        throw new \Exception("Equipamento ID $eid não encontrado.");
                    }
                    if ($equipamento->quantidade_disponivel < $falta) {
                        throw new \Exception("Estoque insuficiente para adicionar {$equipamento->nome}. Faltam: {$falta}.");
                    }

                    for ($i = 0; $i < $falta; $i++) {
                        $novoItem = PedidoProduto::create([
                            'pedido_id'           => $pedido->id,
                            'equipamento_id'      => $eid,
                            'quantidade'          => 1, 
                            'status'              => PedidoProduto::STATUS_RESERVADO,
                            'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                        ]);
                        if (!$novoItem->reservar()) {
                            throw new \Exception("Erro ao reservar {$equipamento->nome}.");
                        }
                    }

                } elseif ($qtdDesejada < $qtdAtual) {

                    $sobra = $qtdAtual - $qtdDesejada;
                    

                    $paraRemover = $itensDesteEquipamento->sortBy(function($item) {

                        return $item->status === PedidoProduto::STATUS_RESERVADO ? 0 : 1;
                    });

                    $removidosCount = 0;
                    foreach ($paraRemover as $item) {
                        if ($removidosCount >= $sobra) break;


                        if ($item->quantidade > 1) {
                            $reducaoNecessaria = $sobra - $removidosCount;
                            $podeTirar = $item->quantidade - 1; 
                            
                            $tirar = min($reducaoNecessaria, $podeTirar);
                            if ($tirar > 0) {
                                if ($item->status === PedidoProduto::STATUS_RESERVADO) {
                                    $item->alterarQuantidade($item->quantidade - $tirar);
                                    $removidosCount += $tirar;
                                } else {

                                }
                            }
                        } 

                        elseif ($item->quantidade == 1) {
                            if ($item->status === PedidoProduto::STATUS_RESERVADO) {
                                $item->cancelar(); 
                                $item->delete(); 
                                $removidosCount++;
                            } else {

                            }
                        }
                    }
                }
            }


            foreach ($itensNoBanco as $eid => $colecaoItens) {
                if (!in_array($eid, $equipamentosProcessados)) {

                    foreach ($colecaoItens as $item) {
                        if ($item->status === PedidoProduto::STATUS_RESERVADO) {
                            $item->cancelar();
                            $item->delete();
                        } else {

                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('pedidos.show', $pedido->id)->with('success', 'Pedido atualizado com sucesso!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->role === 'cliente') {
             abort(403, 'Acesso não autorizado.');
        }

        DB::beginTransaction();
        try {
            $pedido = Pedido::with('itens')->findOrFail($id);

            foreach ($pedido->itens as $item) {
                 if ($item->status === PedidoProduto::STATUS_RESERVADO || $item->status === PedidoProduto::STATUS_EM_LOCACAO) {
                      $item->cancelar();
                 }
            }

            $pedido->delete();

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pedidos.index')->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }


    public function comprovante($id)
    {
        $user = Auth::user();
        $query = Pedido::with(['cliente', 'funcionario', 'itens.equipamento']);

        if ($user->role === 'cliente') {
            $query->where('cliente_id', $user->cliente_id);
        }

        $pedido = $query->find($id);

        if (!$pedido) {
            abort(404, 'Pedido não encontrado ou não pertence a você.');
        }

        return view('pedidos.comprovante', compact('pedido'));
    }

    public function grafico(Pedido $pedido)
    {
        $user = Auth::user();

        if ($user->role === 'cliente' && $pedido->cliente_id !== $user->cliente_id) {
             abort(403, 'Acesso não autorizado.');
        }

        $pedido->load('itens.equipamento');
        $seriesPorItem = [];
        foreach ($pedido->itens as $item) {
            if ($item->status === PedidoProduto::STATUS_CANCELADO) continue;
            $seriesPorItem[$item->id] = $item->breakdownDecorrido();
        }
        $agregado = $this->agruparSeries($seriesPorItem);

        $events = [];
        foreach ($pedido->itens as $item) {
            if ($item->status === PedidoProduto::STATUS_CANCELADO) continue;
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

    public function decorridos(Pedido $pedido)
    {
        $user = Auth::user();

        if ($user->role === 'cliente' && $pedido->cliente_id !== $user->cliente_id) {
             abort(403, 'Acesso não autorizado.');
        }

        $pedido->load('itens.equipamento');
        $series = [];
        foreach ($pedido->itens as $item) {
            if ($item->status === PedidoProduto::STATUS_CANCELADO) continue;
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
        ksort($agrupado);
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
}