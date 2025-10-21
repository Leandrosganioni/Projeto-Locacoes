@extends('layouts.app')

@section('title', 'Detalhes do Pedido')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2"> {{-- Adicionado flex-wrap e gap --}}
        <h2 class="mb-0">Detalhes do Pedido #{{ $pedido->id }}</h2>
        <div class="d-flex flex-wrap gap-2"> {{-- Adicionado flex-wrap e gap --}}
            {{-- Botão Editar Pedido (apenas para funcionários e admins) --}}
            @if(Auth::user()->role !== 'cliente')
                {{-- Verifica se o pedido pode ser editado (nenhum item em locação ou devolvido) --}}
                @php
                    $podeEditar = $pedido->itens->every(fn($item) => $item->status === App\Models\PedidoProduto::STATUS_RESERVADO);
                @endphp
                <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-outline-warning {{ !$podeEditar ? 'disabled' : '' }}"
                   @if(!$podeEditar) title="Não pode ser editado pois contém itens em locação ou devolvidos" @endif>
                    <i class="bi bi-pencil"></i> Editar Pedido
                </a>
            @endif

            {{-- Botões Comprovante e Evolução (visíveis para todos) --}}
            <a href="{{ route('pedidos.comprovante', $pedido->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Imprimir
            </a>
            <a href="{{ route('pedidos.decorridos', $pedido->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history"></i> Evolução diária
            </a>
            <a href="{{ route('pedidos.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="bg-white shadow rounded p-4 mb-4">
        <h5 class="fw-semibold mb-3 border-bottom pb-2">Informações do Pedido</h5>
        <dl class="row mb-0">
            <dt class="col-sm-3">Cliente</dt>
            <dd class="col-sm-9">{{ $pedido->cliente->nome }}</dd>

            {{-- Mostra Funcionário apenas se não for cliente --}}
            @if(Auth::user()->role !== 'cliente')
            <dt class="col-sm-3">Funcionário Responsável</dt>
            <dd class="col-sm-9">{{ $pedido->funcionario?->nome ?? '-' }}</dd>
            @endif

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
                    <th class="text-center">Qtd.</th>
                    <th>Status</th>
                    <th>Retirada</th>
                    <th>Devolução</th>
                    <th class="text-end">Diária (R$)</th>
                    <th class="text-end">Total (R$)</th>
                    {{-- Mostra coluna Ações apenas para funcionários e admins --}}
                    @if(Auth::user()->role !== 'cliente')
                    <th class="text-center">Ações</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($pedido->itens as $item)
                <tr>
                    <td>{{ $item->equipamento->nome }}</td>
                    <td class="text-center">{{ $item->quantidade }}</td>
                    <td>
                        @php
                            $status = $item->status;
                            $badgeClass = match($status) {
                                App\Models\PedidoProduto::STATUS_RESERVADO  => 'warning text-dark',
                                App\Models\PedidoProduto::STATUS_EM_LOCACAO => 'primary',
                                App\Models\PedidoProduto::STATUS_DEVOLVIDO  => 'success',
                                App\Models\PedidoProduto::STATUS_CANCELADO  => 'secondary',
                                default => 'light text-dark'
                            };
                        @endphp
                        <span class="badge bg-{{ $badgeClass }} text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    </td>
                    <td>{{ $item->start_at ? $item->start_at->format('d/m/Y H:i') : '-' }}</td>
                    <td>{{ $item->end_at ? $item->end_at->format('d/m/Y H:i') : '-' }}</td>
                    <td class="text-end">{{ number_format($item->daily_rate_snapshot, 2, ',', '.') }}</td>
                    <td class="text-end">
                        {{-- Usa o helper para calcular e exibir o total --}}
                        @if($item->status === App\Models\PedidoProduto::STATUS_DEVOLVIDO)
                            {{ number_format($item->computed_total, 2, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    {{-- Mostra ações apenas para funcionários e admins --}}
                    @if(Auth::user()->role !== 'cliente')
                    <td class="text-center">
                        <div class="d-flex flex-wrap justify-content-center gap-1">
                            @if($item->status === App\Models\PedidoProduto::STATUS_RESERVADO)
                                <form method="POST" action="{{ route('pedidos.itens.retirar', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Marcar como Retirado">
                                        <i class="bi bi-box-arrow-up"></i> Retirar
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('pedidos.itens.cancelar', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancelar Reserva" onclick="return confirm('Confirmar cancelamento deste item?');">
                                        <i class="bi bi-x-circle"></i> Cancelar
                                    </button>
                                </form>
                            @elseif($item->status === App\Models\PedidoProduto::STATUS_EM_LOCACAO)
                                <form method="POST" action="{{ route('pedidos.itens.devolver', $item->id) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Marcar como Devolvido">
                                        <i class="bi bi-arrow-return-left"></i> Devolver
                                    </button>
                                </form>
                            @else
                                {{-- Nenhuma ação para itens devolvidos ou cancelados --}}
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                 <tr>
                    {{-- Ajusta o número de colunas vazias --}}
                    <td colspan="{{ Auth::user()->role !== 'cliente' ? 8 : 7 }}" class="text-center text-muted py-3">Nenhum item neste pedido.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        @php
            // Calcula o total apenas dos itens devolvidos
            $totalPedidoDevolvido = $pedido->itens
                                        ->where('status', App\Models\PedidoProduto::STATUS_DEVOLVIDO)
                                        ->sum('computed_total');
        @endphp
        <h5 class="fw-semibold">Total (apenas itens devolvidos): R$ {{ number_format($totalPedidoDevolvido, 2, ',', '.') }}</h5>
    </div>

    {{-- Gráfico de evolução do valor do pedido (visível para todos) --}}
    <div class="mt-5">
        <h5 class="fw-semibold mb-3">Evolução do valor do pedido (projeção)</h5>
        <div style="position: relative; height: 400px;">
            <canvas id="graficoPedido" data-url="{{ route('pedidos.grafico', $pedido->id) }}"></canvas>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    /* Estilo para badges com texto escuro em fundo claro */
    .badge.bg-warning.text-dark,
    .badge.bg-light.text-dark { color: #333 !important; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/pedido_grafico.js') }}"></script> 
@endpush