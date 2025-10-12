@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Detalhes do Pedido</h2>
        <div>
            <a href="{{ route('pedidos.decorridos', $pedido->id) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-clock-history"></i> Evolução diária
            </a>
            <a href="{{ route('pedidos.index') }}" class="btn btn-light">
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
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Equipamento</th>
                    <th>Quantidade</th>
                    <th>Status</th>
                    <th>Retirada</th>
                    <th>Devolução</th>
                    <th>Diária (R$)</th>
                    <th>Total (R$)</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pedido->itens as $item)
                <tr>
                    <td>{{ $item->equipamento->nome }}</td>
                    <td>{{ $item->quantidade }}</td>
                    <td>
                        @php
                            $status = $item->status;
                            $badgeClass = match($status) {
                                'reservado' => 'warning',
                                'em_locacao' => 'primary',
                                'devolvido' => 'success',
                                'cancelado' => 'secondary',
                                default => 'light'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    </td>
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
                    <td class="text-center">
                        <div class="d-flex flex-wrap justify-content-center gap-1">
                            @if($item->status === App\Models\PedidoProduto::STATUS_RESERVADO)
                                <form method="POST" action="{{ route('pedidos.itens.retirar', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-box-arrow-up"></i> Retirar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pedidos.itens.cancelar', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirmar cancelamento deste item?');">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </form>
                            @elseif($item->status === App\Models\PedidoProduto::STATUS_EM_LOCACAO)
                                <form method="POST" action="{{ route('pedidos.itens.devolver', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-return-left"></i> Devolver
                                    </button>
                                </form>
                            @endif
                        </div>
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
@endpush