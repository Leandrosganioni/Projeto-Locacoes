@extends('layouts.app') 

@section('title', 'Relatório de Estoque')

@section('content')
<div class="container py-5">

    <div class="bg-white shadow rounded p-4 p-md-5"> <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2 class="mb-0 fw-semibold">Relação do Estoque</h2>
            <a href="{{ route('relatorios.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
                <i class="bi bi-arrow-left"></i> Voltar para Central
            </a>
        </div>

        <section class="mb-5">
            <h3 class="fw-semibold mb-3">Gráfico de Ocupação do Estoque</h3>
            <p class="text-muted mb-4">Visualização da quantidade de itens disponíveis vs. locados.</p>
            <div style="position: relative; height: 400px; width: 100%;">
                
                <canvas id="estoqueChart"
                    data-labels='@json($chartLabels)'
                    data-locada='@json($chartDataLocada)'
                    data-disponivel='@json($chartDataDisponivel)'>
                </canvas>

            </div>
        </section>

        <hr class="my-5">

        <section>
            <h3 class="fw-semibold mb-3">Dados Detalhados do Estoque</h3>
            <p class="text-muted mb-4">
                Tabela detalhada com valores e quantidades. Use os botões acima da tabela para exportar os dados.
            </p>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="relatorioEstoqueTable">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome do Produto</th>
                            <th class="text-end">Valor de Venda (Diária)</th>
                            <th class="text-center">Qtd. Disponível</th>
                            <th class="text-center">Qtd. Máxima (Total)</th>
                            <th class="text-center">Qtd. Locada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estoque as $item)
                            <tr>
                                <td>{{ $item->nome }}</td>
                                <td class="text-end">R$ {{ number_format($item->valor_venda, 2, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantidade_disponivel }}</td>
                                <td class="text-center">{{ $item->quantidade_maxima }}</td>
                                <td class="text-center fw-bold">{{ $item->quantidade_maxima - $item->quantidade_disponivel }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">Nenhum equipamento encontrado.</td>
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
<script src="{{ asset('js/relatorio_estoque_chart.js') }}"></script>

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
    $(document).ready(function() {
        

        $('#relatorioEstoqueTable').DataTable({
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
            order: [[0, 'asc']]
        });

        
        initEstoqueChart('estoqueChart');
        
    });
</script>
@endpush