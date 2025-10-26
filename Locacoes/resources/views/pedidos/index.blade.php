@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-semibold">Lista de Pedidos</h2>
            {{-- Mostra o botão "Novo Pedido" apenas para funcionários e admins --}}
            @if(Auth::user()->role !== 'cliente')
            <a href="{{ route('pedidos.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Novo Pedido
            </a>
            @endif
        </div>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        <div class="table-responsive">
            <table id="pedidos-table" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Cliente</th>
                        {{-- Mostra Funcionário apenas se não for cliente --}}
                        @if(Auth::user()->role !== 'cliente')
                        <th>Funcionário</th>
                        @endif
                        <th>Data de Entrega</th>
                        <th>Local de Entrega</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                    <tr>
                        <td>{{ $pedido->cliente->nome }}</td>
                         {{-- Mostra Funcionário apenas se não for cliente --}}
                        @if(Auth::user()->role !== 'cliente')
                        <td>{{ $pedido->funcionario?->nome ?? '-' }}</td> {{-- Adicionado "?->" para segurança --}}
                        @endif
                        <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                        <td>{{ $pedido->local_entrega }}</td>
                        <td class="text-center">
                            {{-- Botão Ver Detalhes (visível para todos) --}}
                            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-sm btn-outline-info me-1" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>

                            {{-- Botões Editar e Excluir (apenas para funcionários e admins) --}}
                            @if(Auth::user()->role !== 'cliente')
                                <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-outline-warning me-1" title="Editar Pedido">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir Pedido" onclick="return confirm('Deseja excluir este pedido?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Ajusta o número de colunas vazias dependendo do papel --}}
                        <td class="text-center">-</td>
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
        $('#pedidos-table').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' },
            dom: "<'row mb-3'<'col-sm-6'l><'col-sm-6 text-end'B>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row mt-3'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                { extend: 'copyHtml5', className: 'btn btn-sm btn-secondary', text: '<i class="bi bi-clipboard"></i> Copiar' },
                { extend: 'csvHtml5', className: 'btn btn-sm btn-success', text: '<i class="bi bi-filetype-csv"></i> CSV' },
                { extend: 'excelHtml5', className: 'btn btn-sm btn-success', text: '<i class="bi bi-file-earmark-excel"></i> Excel' },
                { extend: 'pdfHtml5', className: 'btn btn-sm btn-danger', text: '<i class="bi bi-file-earmark-pdf"></i> PDF' },
                { extend: 'print', className: 'btn btn-sm btn-primary', text: '<i class="bi bi-printer"></i> Imprimir' }
            ]
        });
    });
</script>
@endpush