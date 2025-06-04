<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Equipamento;
use Illuminate\Http\Request;

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

        $pedido = Pedido::create([
            'cliente_id' => $request->cliente_id,
            'funcionario_id' => $request->funcionario_id,
            'local_entrega' => $request->local_entrega,
            'data_entrega' => $request->data_entrega,
        ]);

        // Associa os produtos com as quantidades
        foreach ($request->produtos as $produtoId) {
            $quantidade = $request->quantidades[$produtoId] ?? 0;

            // Apenas associa se quantidade for maior que zero
            if ($quantidade > 0) {
                $pedido->produtos()->attach($produtoId, ['quantidade' => $quantidade]);
            }
        }

        return redirect()->route('pedidos.index')
            ->with('success', 'Pedido criado com sucesso!');
    }



    public function edit($id)
    {
        $pedido = Pedido::with('produtos')->findOrFail($id);

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

        $pedido = Pedido::findOrFail($id);

        $pedido->update([
            'cliente_id' => $request->cliente_id,
            'funcionario_id' => $request->funcionario_id,
            'local_entrega' => $request->local_entrega,
            'data_entrega' => $request->data_entrega
        ]);

        $pedido->produtos()->detach();
        foreach ($request->produtos as $index => $produto_id) {
            $pedido->produtos()->attach($produto_id, [
                'quantidade' => $request->quantidades[$index]
            ]);
        }

        return redirect()->route('pedidos.index')->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy($id)
    {
        try {
            $pedido = Pedido::findOrFail($id);
            $pedido->produtos()->detach();
            $pedido->delete();

            return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('pedidos.index')->with('error', 'Erro ao excluir pedido: ' . $e->getMessage());
        }
    }
}
