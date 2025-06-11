<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'nome' => 'required',
            'cpf' => 'required',
            'telefone' => 'required',
        ]);

        Funcionario::create($request->all());

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionario cadastrado com sucesso!');
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
            'cpf' => [
                'required',
                'string',
                Rule::unique('funcionarios')->ignore($funcionario->id) //dar uma atenção nisso depois
            ],
            'telefone' => 'required|string|max:20',
    ]);

        $funcionario->update($request->all());

        return redirect()
            ->route('funcionarios.index')
            ->with('success', 'Funcionário atualizado com sucesso!');
    }

    public function destroy(Funcionario $funcionario)
    {
        try {
            $funcionario->delete();
            return redirect()
                ->route('funcionarios.index')
                ->with('success', 'Funcionario excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('funcionarios.index')
                ->with('error', 'Erro ao excluir funcionario: ' . $e->getMessage());
        }
    }
}