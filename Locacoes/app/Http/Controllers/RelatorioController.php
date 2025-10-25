<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Equipamento;

class RelatorioController extends Controller
{
    public function index()
    {
        return view('relatorios.index');
    }

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

    public function relatorioVendas(Request $request)
    {
        $data_inicio = $request->input('data_inicio', Carbon::now()->subDays(30)->toDateString());
        $data_fim = $request->input('data_fim', Carbon::now()->toDateString());
        $data_fim_filtro = $data_fim . ' 23:59:59';
        $limite_top = (int) $request->input('limite_top', 5);
        $limite_minimo = (int) $request->input('limite_minimo', 3);

        $inputs = $request->all();
        $inputs['data_inicio'] = $data_inicio;
        $inputs['data_fim'] = $data_fim;
        $inputs['limite_top'] = $limite_top;
        $inputs['limite_minimo'] = $limite_minimo;

        $queryVendas = DB::table('pedido_produto')
            ->join('pedidos', 'pedido_produto.pedido_id', '=', 'pedidos.id')
            ->join('equipamentos', 'pedido_produto.equipamento_id', '=', 'equipamentos.id')
            ->whereBetween('pedidos.created_at', [$data_inicio, $data_fim_filtro])
            ->select(
                'equipamentos.nome',
                DB::raw('SUM(pedido_produto.quantidade) as total_locado'),
                DB::raw('SUM(pedido_produto.quantidade * pedido_produto.daily_rate_snapshot) as faturamento_total')
            )
            ->groupBy('equipamentos.nome')
            ->orderBy('total_locado', 'desc');

        $queryTopProdutos = clone $queryVendas;
        $relatorioVendas = $queryVendas->get();
        $topProdutos = $queryTopProdutos->limit($limite_top)->get();

        $baixaEstoque = Equipamento::where('quantidade_disponivel', '<=', $limite_minimo)
            ->select('nome', 'quantidade_disponivel', 'quantidade_total')
            ->orderBy('quantidade_disponivel', 'asc')
            ->get();

        $chartTopProdutosLabels = $topProdutos->pluck('nome');
        $chartTopProdutosData = $topProdutos->pluck('total_locado');
        $chartBaixaEstoqueLabels = $baixaEstoque->pluck('nome');
        $chartBaixaEstoqueData = $baixaEstoque->pluck('quantidade_disponivel');

        return view('relatorios.vendas', compact(
            'relatorioVendas',
            'topProdutos',
            'baixaEstoque',
            'chartTopProdutosLabels',
            'chartTopProdutosData',
            'chartBaixaEstoqueLabels',
            'chartBaixaEstoqueData',
            'inputs'
        ));
    }
}