<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\User; //criar o usuário
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //transações
use Illuminate\Support\Facades\Hash; //encriptar a senha
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules; //regras de senha mais robustas

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     * (Exibe a lista de clientes)
     */
    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get(); // Ordenar por nome
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     * (Mostra o formulário para criar um novo cliente)
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Store a newly created resource in storage.
     * (Armazena um novo cliente e seu usuário associado no banco de dados)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:100',
            'cpf_cnpj' => ['required', 'string', 'max:18', Rule::unique('clientes')],
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:200',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::beginTransaction();
        try {
            // 1. Criar o Cliente
            // --- INÍCIO DA CORREÇÃO ---
            $cliente = Cliente::create([
                'nome' => $validated['nome'],
                'cpf_cnpj' => $validated['cpf_cnpj'],
                'telefone' => $validated['telefone'],
                'endereco' => $validated['endereco'],
                // 'email' => $validated['email'], // <-- REMOVIDA ESTA LINHA
            ]);
            // --- FIM DA CORREÇÃO ---

            // 2. Criar o Usuário associado (aqui o email está correto)
            $user = User::create([
                'name' => $validated['nome'],
                'email' => $validated['email'], // O email é salvo aqui
                'password' => Hash::make($validated['password']),
                'role' => 'cliente',
                'cliente_id' => $cliente->id,
                'funcionario_id' => null,
            ]);

            DB::commit();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente e acesso de utilizador cadastrados com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Mostra o erro real para depuração
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     * (Exibe os detalhes de um cliente específico)
     */
    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     * (Mostra o formulário para editar um cliente)
     */
    public function edit(Cliente $cliente)
    {
        // Carrega o usuário associado para preencher o email no formulário de edição (se necessário)
        $cliente->load('user');
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     * (Atualiza os dados de um cliente existente)
     * // TO DO: Ajustar este método para permitir atualizar também o email/senha do usuário associado.
     * // Por enquanto, ele atualiza apenas os dados do cliente.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf' => ['required', 'string', Rule::unique('clientes')->ignore($cliente->id)],
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:200',
            // Validação para email e senha (se for permitir alterar aqui) deve ser adicionada
        ]);

        $cliente->update($request->only(['nome', 'cpf', 'telefone', 'endereco'])); // Atualiza apenas estes campos por enquanto

        // Lógica para atualizar email/senha do usuário associado (se aplicável)
        // ...

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     * (Remove um cliente e seu usuário associado)
     */
    public function destroy(Cliente $cliente)
    {
        DB::beginTransaction();
        try {
            // Encontra o usuário associado e o remove (se existir)
            // Usar ->first() para o caso de não haver usuário associado ainda
            $user = User::where('cliente_id', $cliente->id)->first();
            if ($user) {
                $user->delete();
            }

            // Remove o cliente
            $cliente->delete();

            DB::commit();
            return redirect()->route('clientes.index')
                ->with('success', 'Cliente e utilizador associado excluídos com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Erro ao excluir cliente/usuário: ' . $e->getMessage());
            return redirect()->route('clientes.index')
                ->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }
}
