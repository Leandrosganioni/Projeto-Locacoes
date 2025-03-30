<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    
    public function index()
    {
        $clientes = Cliente::all(); 
        return view('clientes.index', compact('clientes'));
    }

    
    public function create()
    {
        return view('clientes.create');
    }

    
    public function store(Request $request)
    {
        //validar dos dados
        $validated = $request->validate([
            'nome' => 'required',
            'cpf' => 'required',
            'telefone' => 'required',
            'endereco' => 'required'
        ]);

        //Criaro cliente
        Cliente::create($request->all());

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente cadastrado com sucesso!');
    }

    
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    
    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    
    public function update(Request $request, Cliente $cliente)
    {
        
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|size:14|unique:clientes,cpf,'.$cliente->id,
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:200'
        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente atualizado com sucesso!');
    }

    
    public function destroy(Cliente $cliente)
{
    try {
        $cliente->delete();
        return redirect()->route('clientes.index')
                       ->with('success', 'Cliente excluído com sucesso!');
    } catch (\Exception $e) {
        return redirect()->route('clientes.index')
                       ->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
    }
}
}