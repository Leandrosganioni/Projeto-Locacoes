<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    public function index()
    {
        $funcionarios = Funcionario::all();
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create()
    {
        return view('funcionarios.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nome' => 'required|string|max:100',
        'cpf' => 'required|string|size:14|unique:funcionarios',
        'cargo' => 'required|in:Administrativo,Vendas,RH,Marketing'
    ]);

    Funcionario::create([
        'nome' => $request->nome,
        'cpf' => $request->cpf,
        'cargo' => $request->cargo
    ]);

    return redirect()->route('funcionarios.index')
                     ->with('success', 'Funcionário cadastrado com sucesso!');
}

    public function show(Funcionario $funcionario)
    {
        return view('funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario)
    {
        return view('funcionarios.edit', compact('funcionario'));
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|size:14|unique:funcionarios,cpf,'.$funcionario->id,
            'cargo' => 'required|in:Administrativo,Vendas,RH,Marketing,TI'
        ]);

        $funcionario->update($request->all());

        return redirect()->route('funcionarios.index')
                         ->with('success', 'Funcionário atualizado!');
    }

    public function destroy(Funcionario $funcionario)
    {
        $funcionario->delete();
        return redirect()->route('funcionarios.index')
                         ->with('success', 'Funcionário removido!');
    }
}
