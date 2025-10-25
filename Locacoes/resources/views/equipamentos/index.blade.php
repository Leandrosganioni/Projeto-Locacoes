@extends('layouts.app')

@section('title', 'Lista de Equipamentos')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-semibold">Lista de Equipamentos</h2>
            <a href="{{ route('equipamentos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Equipamento
            </a>
        </div>

        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Equipamentos</h2>
        <a href="{{ route('quebras.relatorio') }}" class="btn btn-outline-warning">
            <i class="bi bi-file-earmark-bar-graph"></i> Ver Relatório de Quebras
        </a>
    </div>
    
    {{-- A tabela de listagem de equipamentos deve vir abaixo daqui --}}
    <table class="table table-striped">
        {{-- ... código da tabela ... --}}
    </table>
        </div>

        <div class="table-responsive">
            <table id="equipamentos-table" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width:100px;">Imagem</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th class="text-center">Estoque (Disp/Total)</th>
                        <th>Diária</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($equipamentos as $equipamento)
                    <tr>
                        <td>
                            @if($equipamento->imagem && file_exists(public_path('images/equipamentos/' . $equipamento->imagem)))
                            <img src="{{ asset('images/equipamentos/' . $equipamento->imagem) }}" alt="{{ $equipamento->nome }}" class="img-thumbnail equipamento-imagem">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $equipamento->nome }}</td>
                        <td>{{ $equipamento->tipo }}</td>
                        <td class="text-center">{{ $equipamento->quantidade_disponivel }} / {{ $equipamento->quantidade_total }}</td>
                        <td>R$ {{ number_format($equipamento->daily_rate, 2, ',', '.') }}</td>
                        <td class="text-center">
                            <a href="{{ route('equipamentos.show', $equipamento->id) }}" class="btn btn-sm btn-outline-info me-1">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('equipamentos.destroy', $equipamento->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Deseja excluir este equipamento?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td class="text-center">-</td>
                        <td class="text-center"></td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                    </tr>
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
<style>
    .equipamento-imagem {
        max-height: 80px;
        width: auto;
        transition: transform 0.2s ease-in-out;
    }

    .equipamento-imagem:hover {
        transform: scale(1.5);
        z-index: 10;
        position: relative;
    }
</style>
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
    $(document).ready(function() {
        $('#equipamentos-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [{
                    extend: 'copyHtml5',
                    className: 'btn btn-sm btn-secondary',
                    text: '<i class="bi bi-clipboard"></i> Copiar'
                },
                {
                    extend: 'csvHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="bi bi-filetype-csv"></i> CSV'
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn btn-sm btn-success',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel'
                },
                {
                    extend: 'pdfHtml5',
                    className: 'btn btn-sm btn-danger',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF'
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-primary',
                    text: '<i class="bi bi-printer"></i> Imprimir'
                }
            ]
        });
    });
</script>
@endpush