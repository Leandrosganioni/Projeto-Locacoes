<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- Adicionado
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; // <-- Adicionado
use App\Models\Equipamento;
use App\Models\OcorrenciaEstoque; // <-- Já estava aqui

class RelatorioController extends Controller
{
    public function index()
    {
        return view('relatorios.index');
    }

    public function relatorioEstoque()
    {
        // ... (código existente, sem alterações)
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
        // ... (código existente, sem alterações)
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
            ->whereBetween('pedidos.created_at', [$data_inicio, $data_fim_filtro]) // <-- Atenção: Filtro baseado na criação do PEDIDO, não na devolução do item. Ajustar se necessário.
            ->select(
                'equipamentos.nome',
                DB::raw('SUM(pedido_produto.quantidade) as total_locado'),
                // Considerar usar 'computed_total' da tabela pedido_produto se o faturamento for baseado na devolução
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

    /**
     * Exibe o relatório de quebras e devoluções com filtros e dados para gráficos.
     */
    public function relatorioQuebras(Request $request) // <-- Adicionado Request $request
    {
        // 1. Obter e validar filtros da requisição
        $data_inicio = $request->input('data_inicio', Carbon::now()->subDays(30)->toDateString());
        $data_fim = $request->input('data_fim', Carbon::now()->toDateString());
        $data_fim_filtro = Carbon::parse($data_fim)->endOfDay(); // Garante pegar até o fim do dia
        $equipamento_filtro = $request->input('equipamento_filtro'); // Nome do equipamento

        // 2. Query base para as ocorrências
        $queryOcorrencias = OcorrenciaEstoque::with(['equipamento', 'user', 'cliente', 'pedido'])
                                        ->whereBetween('created_at', [$data_inicio, $data_fim_filtro]);

        // 3. Aplicar filtro de equipamento (se houver)
        if ($equipamento_filtro) {
            // Busca pelo nome do equipamento na tabela relacionada
            $queryOcorrencias->whereHas('equipamento', function ($q) use ($equipamento_filtro) {
                $q->where('nome', 'like', '%' . $equipamento_filtro . '%');
            });
        }

        // Clonar a query ANTES de ordenar e executar, para usar nos gráficos
        $queryGraficos = clone $queryOcorrencias;

        // 4. Buscar os dados detalhados para a tabela (ordenados)
        // Usamos get() em vez de paginate() para DataTables do lado do cliente
        $ocorrencias = $queryOcorrencias->orderBy('created_at', 'desc')->get();

        // 5. Gerar dados para os gráficos (usando a query clonada sem ordenação/limite)

        // Gráfico de Motivos (Pizza/Rosca)
        $ocorrenciasPorMotivo = $queryGraficos
            ->select('motivo', DB::raw('count(*) as total'))
            ->groupBy('motivo')
            ->orderBy('total', 'desc') // Ordena para mostrar os mais comuns primeiro
            ->get();

        // Mapeia para labels mais legíveis
        $chartMotivosLabels = $ocorrenciasPorMotivo->map(function ($item) {
            return ucfirst(str_replace('_', ' ', $item->motivo)); // ex: 'validade_expirada' vira 'Validade expirada'
        })->toArray();
        $chartMotivosData = $ocorrenciasPorMotivo->pluck('total')->toArray();

        // Gráfico de Ocorrências ao Longo do Tempo (Linha)
        $ocorrenciasPorDia = $queryGraficos
             // Agrupa por data (truncando a hora) e conta
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('count(*) as total'))
            ->groupBy('data')
            ->orderBy('data', 'asc') // Ordena pela data para o gráfico de linha
            ->get()
            // Mapeia para o formato [data => total]
            ->pluck('total', 'data');

        // Preenche dias sem ocorrências no período para o gráfico ficar contínuo
        $periodo = Carbon::parse($data_inicio)->daysUntil($data_fim_filtro);
        $chartTempoLabels = [];
        $chartTempoData = [];
        foreach ($periodo as $date) {
            $dataFormatada = $date->toDateString();
            $chartTempoLabels[] = $date->format('d/m'); // Label curto para o eixo X
            $chartTempoData[] = $ocorrenciasPorDia->get($dataFormatada, 0); // Pega o total ou 0 se não houver
        }


        // 6. Retornar a view com todos os dados
        return view('relatorios.quebras', compact(
            'ocorrencias',
            'chartMotivosLabels',
            'chartMotivosData',
            'chartTempoLabels',
            'chartTempoData',
            // Passa os inputs de volta para a view preencher os filtros
            'data_inicio',
            'data_fim',
            'equipamento_filtro'
        ));
    }
}