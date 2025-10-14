@extends('layouts.app')

@section('title', 'Comprovante do Pedido')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h2 class="mb-0">Comprovante do Pedido</h2>
        <div>
            <button type="button" onclick="window.print()" class="btn btn-primary me-2">
                <i class="bi bi-printer"></i> Imprimir
            </button>
            <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4 mb-4">
        <h5 class="fw-semibold mb-3 border-bottom pb-2">Informações do Pedido</h5>
        <dl class="row mb-0">
            <dt class="col-sm-3">Cliente</dt>
            <dd class="col-sm-9">{{ $pedido->cliente->nome }}</dd>

            <dt class="col-sm-3">Funcionário Responsável</dt>
            <dd class="col-sm-9">{{ $pedido->funcionario->nome }}</dd>

            <dt class="col-sm-3">Local de Entrega</dt>
            <dd class="col-sm-9">{{ $pedido->local_entrega }}</dd>

            <dt class="col-sm-3">Data de Entrega</dt>
            <dd class="col-sm-9">{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</dd>
        </dl>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Equipamento</th>
                    <th>Quantidade</th>
                    <th>Status</th>
                    <th>Retirada</th>
                    <th>Devolução</th>
                    <th>Diária (R$)</th>
                    <th>Total (R$)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->itens as $item)
                <tr>
                    <td>{{ $item->equipamento->nome }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td class="text-capitalize">{{ str_replace('_', ' ', $item->status) }}</td>
                    <td>{{ $item->start_at ? $item->start_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->end_at ? $item->end_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ number_format($item->daily_rate_snapshot, 2, ',', '.') }}</td>
                    <td>
                        @if($item->status === 'devolvido')
                            {{ number_format($item->computed_total, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        @php
            $totalPedido = $pedido->itens->filter(fn($it) => $it->status === App\Models\PedidoProduto::STATUS_DEVOLVIDO)
                                        ->sum('computed_total');
        @endphp
        <h5 class="fw-semibold">Total (itens devolvidos): R$ {{ number_format($totalPedido, 2, ',', '.') }}</h5>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    @media print {
        .no-print { display: none !important; }
        body { -webkit-print-color-adjust: exact; }
    }
</style>
@endpush