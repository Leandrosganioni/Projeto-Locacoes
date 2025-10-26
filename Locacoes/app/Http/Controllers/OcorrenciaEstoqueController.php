<?php

namespace App\Http\Controllers;

use App\Models\OcorrenciaEstoque;
use App\Models\Equipamento;
use App\Models\Pedido;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Essencial para a nova consulta
use Illuminate\Validation\ValidationException; // Essencial para retornar o erro

class OcorrenciaEstoqueController extends Controller
{
    /**
     * Mostra o formulário para criar um novo registro de ocorrência (quebra/devolução).
     *
     * @param  \App\Models\Equipamento  $equipamento O equipamento selecionado
     */
    public function create(Equipamento $equipamento, Request $request)
    {
        // Opcional: Tenta buscar Pedido e Cliente se vierem pela URL (útil para devoluções)
        $pedido = null;
        $cliente = null;

        if ($request->has('pedido_id')) {
            $pedido = Pedido::find($request->pedido_id);
            if ($pedido) {
                $cliente = $pedido->cliente;
            }
        } elseif ($request->has('cliente_id')) {
            $cliente = Cliente::find($request->cliente_id);
        }

        // Busca todos os clientes e pedidos (para seleção manual, se necessário)
        $pedidosDoCliente = collect(); // Inicia como coleção vazia

        // Se um cliente foi determinado (pela URL), busca os pedidos dele
        if ($cliente) {
        $pedidosDoCliente = $cliente->pedidos()
                                  ->orderBy('created_at', 'desc') // <-- Corrigido
                                  ->select('id', 'data_entrega') // <-- Corrigido
                                  ->get();
        }

        // Busca *todos* os clientes para o dropdown
        $clientes = Cliente::orderBy('nome')->get();

        // Não passamos mais $pedidos, passamos $pedidosDoCliente
        return view('ocorrencias.create', compact(
            'equipamento', 
            'pedido', 
            'cliente', 
            'clientes', 
            'pedidosDoCliente' // <- Nome alterado
        ));
    }

    /**
     * Salva o novo registro de ocorrência no banco de dados.
     */
    public function store(Request $request)
    {
        // 1. Validação dos Dados (Campos obrigatórios, etc)
        $validated = $request->validate([
            'equipamento_id' => 'required|exists:equipamentos,id',
            'tipo' => 'required|in:quebra,devolucao',
            'motivo' => 'required|in:avaria,defeito,validade_expirada,outro',
            'motivo_outro' => 'nullable|string|max:255|required_if:motivo,outro',
            'quantidade' => 'required|integer|min:1',
            'observacao' => 'nullable|string',
            'pedido_id' => 'nullable|exists:pedidos,id',
            'cliente_id' => 'nullable|exists:clientes,id',
        ]);

        // 2. Busca o Equipamento
        $equipamento = Equipamento::findOrFail($validated['equipamento_id']);

        // 3. Validação de Estoque (Geral - Não pode quebrar mais do que o disponível)
        if ($validated['quantidade'] > $equipamento->quantidade_disponivel) {
            throw ValidationException::withMessages([
                'quantidade' => 'A quantidade informada (' . $validated['quantidade'] . ') é maior que o estoque disponível (' . $equipamento->quantidade_disponivel . ').'
            ]);
        }
        // (Assumindo que uma quebra/devolução com defeito remove permanentemente do estoque)
        if ($validated['quantidade'] > $equipamento->quantidade_total) {
            throw ValidationException::withMessages([
                'quantidade' => 'A quantidade informada (' . $validated['quantidade'] . ') é maior que o estoque total (' . $equipamento->quantidade_total . ').'
            ]);
        }

        // =====================================================================
        // 4. NOVA VALIDAÇÃO (Quantidade x Pedido)
        // =====================================================================
        // Se um pedido_id foi informado, verificamos a quantidade locada
        if (isset($validated['pedido_id']) && $validated['pedido_id']) {
            
            // Consultamos a tabela pivot 'pedido_produto'
            $itemNoPedido = DB::table('pedido_produto')
                                ->where('pedido_id', $validated['pedido_id'])
                                // Na sua migration, a coluna é 'produto_id' referenciando 'equipamentos'
                                ->where('equipamento_id', $validated['equipamento_id']) 
                                ->first();

            $quantidadeNoPedido = $itemNoPedido ? $itemNoPedido->quantidade : 0;

            if ($quantidadeNoPedido == 0) {
                // Se o item não está no pedido, não podemos vincular.
                throw ValidationException::withMessages([
                    'pedido_id' => 'O equipamento selecionado (' . $equipamento->nome . ') não foi encontrado no pedido informado.'
                ]);
            }

            if ($validated['quantidade'] > $quantidadeNoPedido) {
                // A quantidade da ocorrência é maior que a quantidade locada no pedido
                throw ValidationException::withMessages([
                    'quantidade' => 'A quantidade informada (' . $validated['quantidade'] . ') é maior que a quantidade locada para este item no pedido (' . $quantidadeNoPedido . ').'
                ]);
            }
        }
        
        // 5. Usar Transação para garantir consistência (ou salva tudo ou não salva nada)
        try {
            DB::beginTransaction();

            // 5a. Cria o registro da Ocorrência
            OcorrenciaEstoque::create([
                'equipamento_id' => $validated['equipamento_id'],
                'user_id' => Auth::id(), // Pega o ID do usuário logado
                'pedido_id' => $validated['pedido_id'] ?? null,
                'cliente_id' => $validated['cliente_id'] ?? null,
                'tipo' => $validated['tipo'],
                'motivo' => $validated['motivo'],
                'motivo_outro' => $validated['motivo_outro'] ?? null,
                'quantidade' => $validated['quantidade'],
                'observacao' => $validated['observacao'] ?? null,
            ]);

            // 5b. Atualiza o Estoque do Equipamento (Marca como quebrado/indisponível)
            $equipamento->quantidade_disponivel -= $validated['quantidade'];
            $equipamento->quantidade_quebrada += $validated['quantidade'];
            // $equipamento->quantidade_total NÃO muda mais
            $equipamento->save();

            DB::commit();

            // 6. Redireciona com Sucesso
            return redirect()->route('equipamentos.index')
                             ->with('success', 'Quebra/Devolução registrada com sucesso. Estoque atualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Retorna para o formulário com o erro
            return redirect()->back()
                             ->withInput()
                             ->withErrors(['error' => 'Erro ao registrar a ocorrência: ' . $e->getMessage()]);
        }
    }

    // (Outros métodos como index, show, edit, update, destroy continuam aqui...)
}