@extends('layouts.app') @section('title', 'Relatório de Estoque') @section('content')
<div class="container py-5"> <div class="bg-white shadow rounded p-4"> <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-semibold">F_S01: Relação do Estoque</h2> {{-- <a href="{{ route('index') }}" class="btn btn-light">Voltar</a> --}}
        </div>

        <p class="text-muted mb-4">
            Este relatório apresenta a relação de equipamentos em estoque, suas quantidades disponíveis/totais e os respectivos valores de locação diária.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle" id="relatorioEstoqueTable">
                <thead class="table-dark"> <tr>
                        <th>Nome do Produto</th>
                        <th class="text-end">Valor de Venda (Diária)</th> <th class="text-center">Qtd. Disponível</th> <th class="text-center">Qtd. Máxima (Total)</th> </tr>
                </thead>
                <tbody>
                    @forelse ($estoque as $item)
                        <tr>
                            <td>{{ $item->nome }}</td>
                            <td class="text-end">R$ {{ number_format($item->valor_venda, 2, ',', '.') }}</td>
                            <td class="text-center">{{ $item->quantidade_disponivel }}</td>
                            <td class="text-center">{{ $item->quantidade_maxima }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">Nenhum equipamento encontrado.</td> </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
    // Inicialização do DataTables com Botões
    $(document).ready(function() {
        $('#relatorioEstoqueTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' // Tradução para Português
            },
            // Configuração do DOM para posicionar os botões e outros elementos
            dom: "<'row mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-md-end'B>>" + // 'l' (length changing), 'B' (buttons)
                 "<'row'<'col-sm-12'tr>>" + // 't' (table), 'r' (processing display element)
                 "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // 'i' (information), 'p' (pagination)
            buttons: [
                // Botão Copiar
                {
                    extend: 'copyHtml5',
                    className: 'btn btn-sm btn-outline-secondary', // Estilo Bootstrap
                    text: '<i class="bi bi-clipboard"></i> Copiar' // Ícone e texto
                },
                // Botão CSV
                {
                    extend: 'csvHtml5',
                    className: 'btn btn-sm btn-outline-success',
                    text: '<i class="bi bi-filetype-csv"></i> CSV',
                    titleAttr: 'Exportar para CSV' // Dica ao passar o mouse
                },
                // Botão Excel
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-outline-success',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    titleAttr: 'Exportar para Excel'
                },
                // Botão PDF
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-sm btn-outline-danger',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    titleAttr: 'Exportar para PDF',
                    orientation: 'portrait', // 'landscape' ou 'portrait'
                    pageSize: 'A4'
                },
                // Botão Imprimir
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-outline-primary',
                    text: '<i class="bi bi-printer"></i> Imprimir',
                    titleAttr: 'Imprimir Tabela'
                }
            ],
            // Opcional: Ordenar por nome por padrão
            order: [[0, 'asc']]
        });
    });
</script>
@endpush