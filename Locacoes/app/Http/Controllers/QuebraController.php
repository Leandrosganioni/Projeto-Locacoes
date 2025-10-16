<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Material;
use App\Models\DevolucaoEQuebra; // Crie esse Model se ainda não o tiver
use Illuminate\Support\Facades\DB;

class QuebraController extends Controller
{
    /**
     * Exibe o formulário para registrar uma quebra.
     * @param int $pedido_id
     * @return \Illuminate\View\View
     */
    public function create($pedido_id)
    {
        $pedido = Pedido::with('itensDoPedido.material')->findOrFail($pedido_id);
        
        return view('quebras.create', compact('pedido'));
    }

    /**
     * Salva o registro de quebra no banco de dados.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function index()
    {
        // Retorna uma view simples com um campo de busca
        return view('quebras.index'); 
    }

    public function store(Request $request)
    {
        // Validação dos dados
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'material_id' => 'required|exists:materiais,id',
            'quantidade' => 'required|integer|min:1',
            'motivo' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Registra a quebra na nova tabela
            DevolucaoEQuebra::create([
                'pedido_id' => $request->pedido_id,
                'material_id' => $request->material_id,
                'quantidade' => $request->quantidade,
                'motivo' => $request->motivo,
                'tipo' => 'quebra',
                'status' => 'registrada',
            ]);

            // 2. Atualiza a quantidade disponível do material
            $material = Material::findOrFail($request->material_id);
            $material->quantidade_disponivel -= $request->quantidade;
            $material->save();

            DB::commit();

            return redirect()->route('pedidos.index')->with('success', 'Quebra registrada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro ao registrar a quebra. Tente novamente.');
        }
    }
}