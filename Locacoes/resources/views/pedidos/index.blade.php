@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Lista de Pedidos</h1>
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Pedido
        </a>
    </div>

    <div class="table-responsive">
        <table id="pedidos-table" class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Cliente</th>
                    <th>Funcionário</th>
                    <th>Data de Entrega</th>
                    <th>Local de Entrega</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->cliente->nome }}</td>
                        <td>{{ $pedido->funcionario->nome }}</td>
                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                        <td>{{ $pedido->local_entrega }}</td>
                        <td class="text-center">
                            <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-outline-warning me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Deseja excluir este pedido?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Nenhum pedido cadastrado</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- Botões -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#pedidos-table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                },
                dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                     "<'row'<'col-sm-12'tr>>" +
                     "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    { extend: 'copyHtml5', className: 'btn btn-sm btn-secondary me-1', text: '<i class="bi bi-clipboard"></i> Copiar' },
                    { extend: 'csvHtml5', className: 'btn btn-sm btn-success me-1', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                    { extend: 'excelHtml5', className: 'btn btn-sm btn-success me-1', text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
                    { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger me-1', text: '<i class="bi bi-file-earmark-pdf"></i> PDF' },
                    { extend: 'print', className: 'btn btn-sm btn-primary', text: '<i class="bi bi-printer"></i> Imprimir' }
                ]
            });
        });
    </script>
@endpush
