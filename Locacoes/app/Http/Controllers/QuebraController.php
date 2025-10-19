<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Equipamento; 
use App\Models\DevolucaoEQuebra;
use App\Models\PedidoProduto; // Importante adicionar este
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule; // Importante adicionar este

class QuebraController extends Controller
{
    /**
     * Exibe o formulário para registrar uma quebra.
     * @param int $pedido_id
     * @return \Illuminate\View\View
     */
    public function create($pedido_id)
    {
        $pedido = Pedido::with('itens.equipamento')->findOrFail($pedido_id);
        
        return view('quebras.create', compact('pedido'));
    }

    /**
     * Salva o registro de quebra no banco de dados.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function index()
    {
        // Retorna a view de busca
        return view('quebras.index'); 
    }

    public function store(Request $request)
    {
        // --- CORREÇÃO AQUI ---
        
        // 1. Buscamos o item locado primeiro para saber a quantidade máxima
        $itemLocado = PedidoProduto::where('pedido_id', $request->pedido_id)
                                 ->where('equipamento_id', $request->equipamento_id)
                                 ->first();

        // 2. Se não encontrarmos o item, algo está errado, mas definimos 0 como max.
        $maxQuantidade = $itemLocado ? $itemLocado->quantidade : 0;

        // 3. Atualizamos a validação
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'equipamento_id' => [
                'required',
                // Garante que o equipamento_id enviado realmente pertence ao pedido_id enviado
                Rule::exists('pedido_produto')->where(function ($query) use ($request) {
                    $query->where('pedido_id', $request->pedido_id);
                }),
            ],
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                // A quantidade não pode ser maior que a quantidade locada
                "max:{$maxQuantidade}",
            ],
            'motivo' => 'required|string',
        ], [
            // Mensagens de erro amigáveis
            'quantidade.max' => "A quantidade quebrada não pode ser maior que a quantidade locada ({$maxQuantidade} un).",
            'equipamento_id.exists' => 'Este equipamento não faz parte do pedido selecionado.'
        ]);

        // --- FIM DA CORREÇÃO ---

        try {
            DB::beginTransaction();

            // 1. Registra a quebra na nova tabela
            DevolucaoEQuebra::create([
                'pedido_id' => $request->pedido_id,
                'equipamento_id' => $request->equipamento_id,
                'quantidade' => $request->quantidade,
                'motivo' => $request->motivo,
                'tipo' => 'quebra',
                'status' => 'registrada',
            ]);

            // 2. Atualiza a quantidade disponível do equipamento
            $equipamento = Equipamento::findOrFail($request->equipamento_id); 
            
            // ATENÇÃO: Esta lógica diminui o estoque DISPONÍVEL. 
            // Você pode querer diminuir o estoque TOTAL.
            // $equipamento->quantidade_total -= $request->quantidade;
            $equipamento->quantidade_disponivel -= $request->quantidade;
            
            $equipamento->save();

            DB::commit();

            return redirect()->route('pedidos.index')->with('success', 'Quebra registrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro ao registrar a quebra: ' . $e->getMessage());
        }
    }
}