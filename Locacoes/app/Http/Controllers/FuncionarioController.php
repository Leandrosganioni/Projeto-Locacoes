<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // Importe o Hash
use Illuminate\Support\Facades\Auth;

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
        // 1. Validação dos novos campos
        

        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|unique:funcionarios,cpf',
            'telefone' => 'required|string|max:20',
            // Valida o e-mail apenas se o nível de acesso não for 'FUNCIONARIO'
            'email' => 'nullable|email|unique:funcionarios,email',
            // Valida a senha apenas se o nível de acesso não for 'FUNCIONARIO'
            'password' => 'nullable|string|min:8',
            'nivel_acesso' => 'required|string',
        ]);

        // 2. Prepara os dados para criação
        $dados = $request->all();

        // 3. Se a senha foi enviada, cria o hash
        if (!empty($dados['password'])) {
            $dados['password'] = Hash::make($dados['password']);
        }
        Funcionario::create($dados);

        return redirect()
            ->route('funcionarios.index')
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
        // 1. Validação
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => ['required', 'string', Rule::unique('funcionarios')->ignore($funcionario->id)],
            'telefone' => 'required|string|max:20',
            'email' => ['nullable', 'email', Rule::unique('funcionarios')->ignore($funcionario->id)],
            'password' => 'nullable|string|min:8',
            'nivel_acesso' => 'required|string',
        ]);

        // 2. Prepara os dados
        $dados = $request->all();

        // 3. Se uma nova senha foi enviada, cria o hash. Senão, remove o campo para não atualizar.
        if (!empty($dados['password'])) {
            $dados['password'] = Hash::make($dados['password']);
        } else {
            // Remove a chave 'password' para não salvar uma senha vazia no banco
            unset($dados['password']);
        }

        $funcionario->update($dados);

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
                ->with('success', 'Funcionário excluído com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->route('funcionarios.index')
                ->with('error', 'Erro ao excluir funcionário: ' . $e->getMessage());
        }
    }
    public function showConta()
    {
        $funcionario = Auth::user();
        return view('funcionarios.info', compact('funcionario'));
    }
}
