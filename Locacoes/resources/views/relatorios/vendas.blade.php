@extends('layouts.app')

@section('title', 'Relatório de Vendas')

@section('content')
<div class="container py-5">

    <div class="bg-white shadow rounded p-4 p-md-5">

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2 class="mb-0 fw-semibold">Relação de Vendas (Locações)</h2>
            <a href="{{ route('relatorios.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
                <i class="bi bi-arrow-left"></i> Voltar para Central
            </a>
        </div>

        <section class="mb-4 p-4 border rounded bg-light">
            <h5 class="fw-semibold mb-3">Filtros do Relatório</h5>
            <form method="GET" action="{{ route('relatorios.vendas') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label small">Data Início</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="{{ $inputs['data_inicio'] }}">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label small">Data Fim</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" value="{{ $inputs['data_fim'] }}">
                    </div>
                    <div class="col-md-2">
                        <label for="limite_top" class="form-label small">Top N Produtos</label>
                        <input type="number" class="form-control" id="limite_top" name="limite_top" value="{{ $inputs['limite_top'] }}" min="1">
                    </div>
                    <div class="col-md-2">
                        <label for="limite_minimo" class="form-label small">Baixa Estoque (Qtd <=)</label>
                        <input type="number" class="form-control" id="limite_minimo" name="limite_minimo" value="{{ $inputs['limite_minimo'] }}" min="0">
                    </div>
                    <div class="col-md-2 d-flex">
                        <button type="submit" class="btn btn-primary w-100 me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('relatorios.vendas') }}" class="btn btn-light" title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <hr class="my-5">

        <section class="mb-5">
            <h3 class="fw-semibold mb-4">Dashboard Visual</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="text-center mb-3">Top {{ $inputs['limite_top'] }} Produtos Mais Locados</h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="topProdutosChart"
                            data-labels='@json($chartTopProdutosLabels)'
                            data-data='@json($chartTopProdutosData)'>
                        </canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-center mb-3">Alerta de Baixa Estoque (Qtd. <= {{ $inputs['limite_minimo'] }})</h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        <canvas id="baixaEstoqueChart"
                            data-labels='@json($chartBaixaEstoqueLabels)'
                            data-data='@json($chartBaixaEstoqueData)'>
                        </canvas>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5">

        <section class="mb-5">
            <h3 class="fw-semibold mb-4">Resumos Gerenciais</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <h6><i class="bi bi-star-fill text-warning"></i> Produtos Mais Locados</h6>
                    <table class="table table-sm table-striped table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd. Locada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProdutos as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td class="text-center">{{ $item->total_locado }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">Nenhum dado no período.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="bi bi-exclamation-triangle-fill text-danger"></i> Alerta de Baixa Estoque</h6>
                    <table class="table table-sm table-striped table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd. Disponível</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($baixaEstoque as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    <td class="text-center fw-bold text-danger">{{ $item->quantidade_disponivel }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-center text-muted">Nenhum item em baixa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <hr class="my-5">

        <section>
            <h3 class="fw-semibold mb-3">Relatório de Saída de Estoque (Completo)</h3>
            <p class="text-muted mb-4">
                Total de locações e faturamento por produto no período de 
                <strong>{{ \Carbon\Carbon::parse($inputs['data_inicio'])->format('d/m/Y') }}</strong> a 
                <strong>{{ \Carbon\Carbon::parse($inputs['data_fim'])->format('d/m/Y') }}</strong>.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="vendasTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome do Produto</th>
                            <th class="text-center">Total de Saídas (Qtd. Locada)</th>
                            <th class="text-end">Faturamento Total (R$)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($relatorioVendas as $item)
                            <tr>
                                <td>{{ $item->nome }}</td>
                                <td class="text-center">{{ $item->total_locado }}</td>
                                <td class="text-end">R$ {{ number_format($item->faturamento_total, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">Nenhuma locação encontrada no período selecionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    /**
     * Inicializa um Gráfico de Pizza/Rosca (Doughnut)
     */
    function initDoughnutChart(canvasId) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        
        const labels = JSON.parse(ctx.dataset.labels || '[]');
        const data = JSON.parse(ctx.dataset.data || '[]');
        
        if(data.length === 0) {
            ctx.getContext('2d').fillText("Sem dados para exibir.", ctx.width / 2 - 40, ctx.height / 2);
            return;
        }

        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantidade Locada',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    /**
     * Inicializa um Gráfico de Barras
     */
    function initBarChart(canvasId) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;

        const labels = JSON.parse(ctx.dataset.labels || '[]');
        const data = JSON.parse(ctx.dataset.data || '[]');

        if(data.length === 0) {
            ctx.getContext('2d').fillText("Nenhum item em baixa.", ctx.width / 2 - 40, ctx.height / 2);
            return;
        }

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Qtd. Disponível',
                    data: data,
                    backgroundColor: 'rgba(217, 83, 79, 0.7)', // Vermelho (cor de alerta)
                    borderColor: 'rgba(217, 83, 79, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } // Esconde a legenda
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Quantidade' }
                    }
                }
            }
        });
    }

    
    $(document).ready(function() {
        
        
        $('#vendasTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-end'B>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [
                { extend: 'copyHtml5',   className: 'btn btn-sm btn-dark',    text: '<i class="bi bi-clipboard"></i> Copiar' },
                { extend: 'csvHtml5',    className: 'btn btn-sm btn-success', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                { extend: 'excelHtml5',  className: 'btn btn-sm btn-success', text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
                { extend: 'pdfHtml5',    className: 'btn btn-sm btn-danger',  text: '<i class="bi bi-file-earmark-pdf"></i> PDF' },
                { extend: 'print',       className: 'btn btn-sm btn-info',    text: '<i class="bi bi-printer"></i> Imprimir' }
            ],
            
            order: [[1, 'desc']] 
        });

       
        initDoughnutChart('topProdutosChart');
        initBarChart('baixaEstoqueChart');
    });
</script>
@endpush