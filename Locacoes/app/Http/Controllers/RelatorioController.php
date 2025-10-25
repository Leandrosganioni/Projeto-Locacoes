<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;

class RelatorioController extends Controller
{
    /**
     * Exibe a central de relatórios (HUB).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('relatorios.index'); 
    }

    /**
     * Exibe o relatório de relação de estoque.
     *
     * @return \Illuminate\View\View
     */
    public function relatorioEstoque()
    {
        
        $estoque = Equipamento::select(
            'nome',
            'daily_rate as valor_venda',
            'quantidade_disponivel',
            'quantidade_total as quantidade_maxima'
        )
        ->where('quantidade_total', '>', 0)
        ->orderBy('nome')
        ->get();

        
        $itensParaGrafico = $estoque->filter(function ($item) {
            return $item->quantidade_maxima > 0;
        });

        $chartLabels = $itensParaGrafico->pluck('nome');
        $chartDataDisponivel = $itensParaGrafico->pluck('quantidade_disponivel');
        $chartDataLocada = $itensParaGrafico->map(function ($item) {
            return $item->quantidade_maxima - $item->quantidade_disponivel;
        });

        
        return view('relatorios.estoque', compact(
            'estoque',
            'chartLabels',
            'chartDataDisponivel',
            'chartDataLocada'
        ));
    }
}