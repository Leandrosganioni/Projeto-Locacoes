<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PedidoController extends Controller
{
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
        'quantidades' => 'required|array'
    ]);

    $pedido = Pedido::create([
        'cliente_id' => $request->cliente_id,
        'funcionario_id' => $request->funcionario_id,
        'local_entrega' => $request->local_entrega,
        'data_entrega' => $request->data_entrega
    ]);

    foreach ($request->produtos as $index => $produto_id) {
        $pedido->produtos()->attach($produto_id, [
            'quantidade' => $request->quantidades[$index]
        ]);
    }

    return redirect()->back()->with('success', 'Pedido criado com sucesso');
}

}
