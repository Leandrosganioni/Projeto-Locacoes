<?php

namespace App\Http\Controllers;

use App\Models\Funcionario;
use App\Models\User; // <-- Adicionado
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <-- Adicionado
use Illuminate\Support\Facades\Hash; // <-- Adicionado
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules; // <-- Adicionado

class FuncionarioController extends Controller
{
    public function index()
    {
        // Ordenar por nome na listagem
        $funcionarios = Funcionario::orderBy('nome')->get();
        return view('funcionarios.index', compact('funcionarios'));
    }

    public function create()
    {
        return view('funcionarios.create');
    }

    public function store(Request $request)
    {
        $dados = $request->all();
        $dados['cpf'] = preg_replace('/[^0-9]/', '', $dados['cpf']);
        $dados['telefone'] = preg_replace('/[^0-9]/', '', $dados['telefone']);
        
        $request->replace($dados);
        // --- Documentação (Validação) ---
        // Adicionamos validação para os campos de funcionário e os novos campos de usuário/acesso.
        // - role: obrigatório e deve ser 'admin' ou 'funcionario'.
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => ['required', 'string', 'max:14', Rule::unique('funcionarios')], // CPF único na tabela funcionarios
            'telefone' => 'required|string|max:20',
            // Adicione validação para outros campos de funcionário aqui (ex: 'cargo' => 'nullable|string|max:50')
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)], // Email único na tabela users
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Senha confirmada e padrão
            'role' => ['required', Rule::in(['admin', 'funcionario'])], // Papel deve ser um dos valores permitidos
        ]);

        // --- Documentação (Transação) ---
        DB::beginTransaction();
        try {
            // 1. Criar o Funcionário
            $funcionario = Funcionario::create([
                'nome' => $validated['nome'],
                'cpf' => $validated['cpf'],
                'telefone' => $validated['telefone'],
                // Adicione outros campos específicos aqui: 'cargo' => $validated['cargo'] ?? null,
            ]);

            // 2. Criar o Usuário associado
            $user = User::create([
                'name' => $validated['nome'], // Nome do usuário = nome do funcionário
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']), // Encripta a senha
                'role' => $validated['role'], // Papel definido pelo formulário ('admin' ou 'funcionario')
                'funcionario_id' => $funcionario->id, // Vincula o usuário ao funcionário
                'cliente_id' => null, // Garante que não está vinculado a um cliente
            ]);

            DB::commit();

            return redirect()->route('funcionarios.index')
                             ->with('success', 'Funcionário e acesso de utilizador cadastrados com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao cadastrar funcionário/usuário: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao cadastrar o funcionário. Tente novamente.')
                             ->withInput(); // Mantém os dados no formulário
        }
    }

    public function show(Funcionario $funcionario)
    {
        return view('funcionarios.show', compact('funcionario'));
    }

    public function edit(Funcionario $funcionario)
    {
        // Carrega o usuário associado para preencher email/role no form de edição (se necessário)
        $funcionario->load('user');
        return view('funcionarios.edit', compact('funcionario'));
    }

    public function update(Request $request, Funcionario $funcionario)
    {
        // --- Documentação (Validação Update) ---
        // Valida os campos, permitindo que o CPF e Email sejam iguais aos do próprio funcionário/usuário
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => ['required', 'string', 'max:14', Rule::unique('funcionarios')->ignore($funcionario->id)],
            'telefone' => 'required|string|max:20',
            // Validação para outros campos de funcionário
            // --- Validação para campos de usuário (se permitir editar aqui) ---
            // 'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($funcionario->user?->id)],
            // 'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Senha opcional na edição
            // 'role' => ['required', Rule::in(['admin', 'funcionario'])],
        ]);

        DB::beginTransaction();
        try {
            // 1. Atualizar dados do Funcionário
            $funcionario->update([
                'nome' => $validated['nome'],
                'cpf' => $validated['cpf'],
                'telefone' => $validated['telefone'],
                // Atualizar outros campos...
            ]);

            // 2. Atualizar dados do Usuário (se estiverem sendo editados aqui)
            // TODO: Implementar a lógica de atualização do usuário se necessário
            /*
            if ($funcionario->user) {
                $userData = [
                    'name' => $validated['nome'],
                    'email' => $validated['email'],
                    'role' => $validated['role'],
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $funcionario->user->update($userData);
            }
            */

            DB::commit();

            return redirect()->route('funcionarios.index')
                             ->with('success', 'Funcionário atualizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao atualizar funcionário/usuário: ' . $e->getMessage());
            return redirect()->back()
                             ->with('error', 'Erro ao atualizar o funcionário. Tente novamente.')
                             ->withInput();
        }
    }


    public function destroy(Funcionario $funcionario)
    {
        DB::beginTransaction();
        try {
            // Encontra e remove o usuário associado primeiro
            $user = $funcionario->user; // Usa a relação definida no Model
            if ($user) {
                $user->delete();
            }

            // Remove o funcionário
            $funcionario->delete();

            DB::commit();
            return redirect()->route('funcionarios.index')
                           ->with('success', 'Funcionário e utilizador associado excluídos com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao excluir funcionário/usuário: ' . $e->getMessage());
            return redirect()->route('funcionarios.index')
                           ->with('error', 'Erro ao excluir funcionário: ' . $e->getMessage());
        }
    }
}