<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipamento; 

class RelatorioController extends Controller
{
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
        ->orderBy('nome')
        ->get();

        return view('relatorios.estoque', compact('estoque'));
    }
}