@extends('layouts.app')

@section('title', 'Evolução Diária do Pedido')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Evolução Diária do Pedido</h2>
        <a href="{{ route('pedidos.show', $pedido->id) }}" class="btn btn-light">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="mb-4">
        <h5 class="fw-semibold mb-1">Pedido #{{ $pedido->id }}</h5>
        <p class="text-muted mb-0">Cliente: {{ $pedido->cliente->nome }}</p>
    </div>

    <div class="bg-white shadow rounded p-3 mb-5">
        <h5 class="fw-semibold mb-3">Série Agregada (Todos os Itens)</h5>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Data</th>
                        <th>Total Diário (R$)</th>
                        <th>Acumulado (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agregado as $linha)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($linha['data'])->format('d/m/Y') }}</td>
                        <td>{{ number_format($linha['total'], 2, ',', '.') }}</td>
                        <td>{{ number_format($linha['acumulado'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($series as $itemId => $serie)
    @php
        $item = $pedido->itens->firstWhere('id', $itemId);
    @endphp
    <div class="bg-white shadow rounded p-3 mb-4">
        <h5 class="fw-semibold mb-3">Item {{ $item?->equipamento->nome ?? '#'.$itemId }}</h5>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Horas</th>
                        <th>Parcela (R$)</th>
                        <th>Regra</th>
                        <th>Acumulado (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serie as $linha)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($linha['data'])->format('d/m/Y') }}</td>
                        <td>{{ $linha['horas'] }}</td>
                        <td>{{ number_format($linha['parcela'], 2, ',', '.') }}</td>
                        <td class="text-capitalize">{{ $linha['regra'] }}</td>
                        <td>{{ number_format($linha['acumulado'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush