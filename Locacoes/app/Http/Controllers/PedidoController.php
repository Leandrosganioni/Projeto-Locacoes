<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Equipamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with(['cliente', 'funcionario'])->get();
        return view('pedidos.index', compact('pedidos'));
    }

    public function create()
    {
        return view('pedidos.create', [
            'clientes' => Cliente::all(),
            'funcionarios' => Funcionario::all(),
            'produtos' => Equipamento::all()
        ]);
    }

    public function store(Request $request)
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
            $pedido = Pedido::create([
                'cliente_id' => $request->cliente_id,
                'funcionario_id' => $request->funcionario_id,
                'local_entrega' => $request->local_entrega,
                'data_entrega' => $request->data_entrega
            ]);

            // 1) VERIFICA disponibilidade (sem abater aqui)
            foreach ($request->produtos as $equipamento_id) {
                $quantidade = (int)($request->quantidades[$equipamento_id] ?? 0);
                $equipamento = Equipamento::find($equipamento_id);
                $nome = $equipamento ? $equipamento->nome : "ID {$equipamento_id}";

                if (!$equipamento) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Equipamento não encontrado: {$nome}.");
                }

                if ($equipamento->quantidade_disponivel < $quantidade) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Estoque insuficiente para o equipamento {$nome}.");
                }
            }

            // 2) CRIA item + RESERVA via model (é aqui que baixa disponibilidade)
            foreach ($request->produtos as $equipamento_id) {
                $quantidade  = (int)($request->quantidades[$equipamento_id] ?? 0);
                $equipamento = Equipamento::find($equipamento_id);

                $item = PedidoProduto::create([
                    'pedido_id'           => $pedido->id,
                    'equipamento_id'      => $equipamento_id,
                    'quantidade'          => $quantidade,
                    'status'              => PedidoProduto::STATUS_RESERVADO,
                    'daily_rate_snapshot' => (float)($equipamento->daily_rate ?? 0),
                ]);

                if (!$item->reservar()) { // aqui reduz quantidade_disponivel usando Equipamento::reservar
                    DB::rollBack();
                    return redirect()->back()->with('error', "Falha ao reservar {$equipamento->nome}.");
                }
            }

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao criar pedido: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pedido = Pedido::with('itens')->findOrFail($id);

        return view('pedidos.edit', [
            'pedido' => $pedido,
            'clientes' => Cliente::all(),
            'funcionarios' => Funcionario::all(),
            'produtos' => Equipamento::all()
        ]);
    }

    /**
     * Exibe os detalhes de um pedido, incluindo seus itens e informações relacionadas.
     * Esta página serve como a visualização completa do pedido, permitindo ver
     * cliente, funcionário e itens com botões de ações (reservar, retirar, devolver, cancelar).
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Carrega o pedido com relações para exibição completa
        $pedido = Pedido::with(['cliente', 'funcionario', 'itens.equipamento'])->findOrFail($id);
        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Mostra a evolução diária (decorridos) dos itens de um pedido.
     * Para cada item, gera uma série de dados dia-a-dia contendo a parcela calculada
     * e o acumulado. Também gera uma série agregada somando todos os itens por data.
     *
     * @param  \App\Models\Pedido  $pedido
     * @return \Illuminate\Contracts\View\View
     */
    public function decorridos(Pedido $pedido)
    {
        $pedido->load('itens.equipamento');
        $series = [];
        foreach ($pedido->itens as $item) {
            // gera série por item utilizando a lógica da model
            $series[$item->id] = $item->breakdownDecorrido();
        }
        // Série agregada: soma os valores por data
        $agregado = $this->agruparSeries($series);
        return view('pedidos.decorridos', compact('pedido', 'series', 'agregado'));
    }

    /**
     * Agrupa várias séries de itens em uma única série agregada por data.
     * Cada item da entrada deve ser um array indexado por item contendo arrays com
     * chaves 'data' e 'parcela'. O retorno é um array onde cada elemento possui
     * a data e a soma das parcelas daquela data, além do acumulado progressivo.
     *
     * @param  array $series
     * @return array
     */
    protected function agruparSeries(array $series): array
    {
        $agrupado = [];
        // Itera todas as séries e soma por data
        foreach ($series as $itemId => $dados) {
            foreach ($dados as $linha) {
                $data = $linha['data'];
                if (!isset($agrupado[$data])) {
                    $agrupado[$data] = 0.0;
                }
                $agrupado[$data] += (float)($linha['parcela'] ?? 0);
            }
        }
        // Ordena por data e calcula acumulado
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

    public function update(Request $request, $id)
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
        $pedido = Pedido::with('itens.equipamento')->findOrFail($id);

        // 1) DEVOLVE estoque dos itens atuais de forma consistente
        foreach ($pedido->itens as $item) {
            // se o item estiver ‘em_locacao’, você pode decidir bloquear update ou devolver()
            $item->cancelar(); // libera a quantidade_disponivel
        }

        // 2) Atualiza dados do pedido
        $pedido->update([
            'cliente_id' => $request->cliente_id,
            'funcionario_id' => $request->funcionario_id,
            'local_entrega' => $request->local_entrega,
            'data_entrega' => $request->data_entrega
        ]);

        // 3) Verifica disponibilidade dos novos itens
        foreach ($request->produtos as $equipamento_id) {
            $quantidade = (int)($request->quantidades[$equipamento_id] ?? 0);
            $equipamento = Equipamento::find($equipamento_id);
            if (!$equipamento || $equipamento->quantidade_disponivel < $quantidade) {
                DB::rollBack();
                return back()->with('error', "Estoque insuficiente para o equipamento {$equipamento->nome}.");
            }
        }

        // 4) Recria itens e reserva via model
        // (opcional: limpar registros antigos do banco)
        PedidoProduto::where('pedido_id', $pedido->id)->delete();

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
        $pedido = Pedido::with('itens')->findOrFail($id);

        // libera estoque de cada item (se estiver reservado)
        foreach ($pedido->itens as $item) {
            // se estiver em_locacao, você pode decidir devolver() ou impedir exclusão
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
}

}