<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Equipamento; 
use App\Models\DevolucaoEQuebra;
use App\Models\PedidoProduto;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuebraController extends Controller
{
    public function create($pedido_id)
    {
        $pedido = Pedido::with('itens.equipamento')->findOrFail($pedido_id); 
        return view('quebras.create', compact('pedido'));
    }

    public function index()
    {
        return view('quebras.index'); 
    }

    public function relatorio()
    {
        $ocorrencias = DevolucaoEQuebra::with('pedido', 'equipamento')
                                        ->orderBy('created_at', 'desc')
                                        ->get();
        
        return view('quebras.relatorio', compact('ocorrencias'));
    }

    public function store(Request $request)
    {
        $itemLocado = PedidoProduto::where('pedido_id', $request->pedido_id)
                                 ->where('equipamento_id', $request->material_id)
                                 ->first();

        $maxQuantidade = $itemLocado ? $itemLocado->quantidade : 0;

        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',

            'material_id' => [ 
                'required',
                Rule::exists('pedido_produto', 'equipamento_id')->where(function ($query) use ($request) {
                    $query->where('pedido_id', $request->pedido_id);
                }),
            ],
            
            'quantidade' => [
                'required',
                'integer',
                'min:1',
                "max:{$maxQuantidade}",
            ],
            'motivo' => 'required|string',
        ], [
            'quantidade.max' => "A quantidade quebrada não pode ser maior que a quantidade locada ({$maxQuantidade} un).",
            'material_id.exists' => 'Este equipamento não faz parte do pedido selecionado.'
        ]);

        try {
            DB::beginTransaction();

            DevolucaoEQuebra::create([
                'pedido_id' => $request->pedido_id,
                'material_id' => $request->material_id,
                'quantidade' => $request->quantidade,
                'motivo' => $request->motivo,
                'tipo' => 'quebra',
                'status' => 'registrada',
            ]);

            $equipamento = Equipamento::findOrFail($request->material_id);
            $equipamento->quantidade_disponivel -= $request->quantidade;
            $equipamento->save();

            DB::commit();

            return redirect()->route('pedidos.index')->with('success', 'Quebra registrada com sucesso e estoque atualizado!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocorreu um erro ao registrar a quebra: ' . $e->getMessage());
        }
    }
}