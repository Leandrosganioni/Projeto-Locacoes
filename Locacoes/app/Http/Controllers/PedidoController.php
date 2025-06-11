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
                $quantidade = $request->quantidades[$equipamento_id] ?? 0;

                if ($quantidade <= 0) {
                    continue;
                }

                $equipamento = Equipamento::find($equipamento_id);

                if (!$equipamento || $equipamento->quantidade < $quantidade) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Estoque insuficiente para o equipamento {$equipamento->nome}.");
                }

                $equipamento->quantidade -= $quantidade;
                $equipamento->save();

                PedidoProduto::create([
                    'pedido_id' => $pedido->id,
                    'equipamento_id' => $equipamento_id,
                    'quantidade' => $quantidade
                ]);
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'funcionario_id' => 'required|exists:funcionarios,id',
            'local_entrega' => 'required|string',
            'data_entrega' => 'required|date',
            'produtos' => 'required|array',
            'quantidades' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            $pedido = Pedido::with('itens')->findOrFail($id);

            //devolvendo ao pedido antigo
            foreach ($pedido->itens as $item) {
                $equipamento = Equipamento::find($item->equipamento_id);
                if ($equipamento) {
                    $equipamento->quantidade += $item->quantidade;
                    $equipamento->save();
                }
            }

            //update
            $pedido->update([
                'cliente_id' => $request->cliente_id,
                'funcionario_id' => $request->funcionario_id,
                'local_entrega' => $request->local_entrega,
                'data_entrega' => $request->data_entrega
            ]);

            //removing
            PedidoProduto::where('pedido_id', $pedido->id)->delete();

            //add itens e estoque
            foreach ($request->produtos as $equipamento_id) {
                $quantidade = $request->quantidades[$equipamento_id] ?? 0;

                if ($quantidade <= 0) {
                    continue;
                }

                $equipamento = Equipamento::find($equipamento_id);

                if (!$equipamento || $equipamento->quantidade < $quantidade) {
                    DB::rollBack();
                    return redirect()->back()->with('error', "Estoque insuficiente para o equipamento {$equipamento->nome}.");
                }

                $equipamento->quantidade -= $quantidade;
                $equipamento->save();

                PedidoProduto::create([
                    'pedido_id' => $pedido->id,
                    'equipamento_id' => $equipamento_id,
                    'quantidade' => $quantidade
                ]);
            }

            DB::commit();
            return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage());
        }
    }



    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pedido = Pedido::with('itens')->findOrFail($id);

            //devolvendo ao excluir os produtos
            foreach ($pedido->itens as $item) {
                $equipamento = Equipamento::find($item->equipamento_id);
                if ($equipamento) {
                    $equipamento->quantidade += $item->quantidade;
                    $equipamento->save();
                }
            }

            //remove all
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
