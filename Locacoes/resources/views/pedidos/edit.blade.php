@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold">Editar Pedido</h2>

    @if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $erro)
            <li>{{ $erro }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('pedidos.update', $pedido->id) }}">
        @csrf
        @method('PUT')

        <!-- Informações do pedido -->
        <div class="bg-white shadow rounded p-4 mb-5">
            <h5 class="mb-4 fw-semibold border-bottom pb-2">Informações do Pedido</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    @php
                        // Localiza o nome do cliente atual na lista de clientes
                        $clienteAtual = $clientes->firstWhere('id', $pedido->cliente_id);
                    @endphp
                    <input type="text" class="form-control" value="{{ $clienteAtual->nome ?? '' }}" disabled>
                    <input type="hidden" name="cliente_id" value="{{ $pedido->cliente_id }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Funcionário Responsável</label>
                    @php
                        // Localiza o nome do funcionário atual na lista de funcionários
                        $funcionarioAtual = $funcionarios->firstWhere('id', $pedido->funcionario_id);
                    @endphp
                    <input type="text" class="form-control" value="{{ $funcionarioAtual->nome ?? '' }}" disabled>
                    <input type="hidden" name="funcionario_id" value="{{ $pedido->funcionario_id }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Local de Entrega</label>
                    <input type="text" class="form-control" name="local_entrega" value="{{ old('local_entrega', $pedido->local_entrega) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Data de Entrega</label>
                    <input type="date" class="form-control" name="data_entrega" value="{{ old('data_entrega', $pedido->data_entrega) }}" required>
                </div>
            </div>
        </div>

        <!-- Lista de equipamentos -->
        <div class="bg-white shadow rounded p-4 mb-5">
            <h5 class="mb-4 fw-semibold border-bottom pb-2">Equipamentos Disponíveis</h5>
            <div class="mb-4">
                <input type="text" class="form-control" id="busca-equipamento-edit" placeholder="Buscar equipamento...">
            </div>
            <div class="row">
                @foreach($produtos as $produto)
                @php
                    // verifica se o produto já está no pedido
                    $item = $pedido->itens->firstWhere('equipamento_id', $produto->id);
                    $quantidade = $item?->quantidade;
                    $checked = $quantidade ? true : false;
                    $statusItem = $item?->status;
                    // item editável apenas se estiver reservado (ou ainda não existente)
                    $editable = !$statusItem || $statusItem === 'reservado';
                @endphp
                <div class="col-md-3 col-sm-6 mb-4 equipamento-card" data-nome="{{ Str::slug($produto->nome) }}">
                    <div class="card h-100 border-0 shadow-sm rounded">
                        @if(!empty($produto->imagem))
                        <img src="{{ asset('images/equipamentos/' . $produto->imagem) }}" class="card-img-top rounded-top" alt="{{ $produto->nome }}" style="height: 170px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h6 class="fw-bold mb-1">{{ $produto->nome }}</h6>
                            <small class="text-muted d-block mb-2">Disponível: {{ $produto->quantidade_disponivel }} · Diária: R$ {{ number_format($produto->daily_rate, 2, ',', '.') }}</small>
                            @if($editable)
                            <div class="form-check small mb-1">
                                <input class="form-check-input toggle-qtd" type="checkbox" value="{{ $produto->id }}" name="produtos[]" id="produto_{{ $produto->id }}" @if($checked) checked @endif>
                                <label class="form-check-label" for="produto_{{ $produto->id }}">Selecionar</label>
                            </div>
                            <input type="number" name="quantidades[{{ $produto->id }}]" class="form-control form-control-sm quantidade-input mt-2" placeholder="Qtd" min="1" max="{{ $produto->quantidade_disponivel }}" value="{{ old('quantidades.' . $produto->id, $quantidade) }}" @if(!$checked) style="display: none;" disabled @endif>
                            @else
                            @if($checked)
                            <div class="form-check small mb-1">
                                <input class="form-check-input" type="checkbox" checked disabled>
                                <label class="form-check-label">Não editável ({{ $statusItem }})</label>
                            </div>
                            <input type="number" class="form-control form-control-sm mt-2" value="{{ $quantidade }}" disabled>
                            @else
                            <div class="form-check small mb-1 text-muted">Indisponível</div>
                            @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Atualizar Pedido</button>
            <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary px-4">Cancelar</a>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card:hover {
        transform: translateY(-3px);
        transition: 0.2s ease-in-out;
    }
    .select2-container .select2-selection--single {
        height: 38px;
        padding: 6px 12px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('.select2').select2({
            width: '100%',
            placeholder: 'Pesquisar...',
            allowClear: true
        });

        // Alterna exibição do campo de quantidade ao marcar/desmarcar checkbox
        document.querySelectorAll('.toggle-qtd').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const container = this.closest('.card-body');
                const input = container.querySelector('.quantidade-input');
                if (this.checked) {
                    input.style.display = 'block';
                    input.disabled = false;
                    if (!input.value || +input.value < 1) input.value = 1;
                } else {
                    input.style.display = 'none';
                    input.disabled = true;
                    input.value = '';
                }
            });
        });

        // Filtro de busca por nome
        document.getElementById('busca-equipamento-edit').addEventListener('input', function() {
            const termo = this.value.toLowerCase().trim().replace(/\s+/g, '-');
            document.querySelectorAll('.equipamento-card').forEach(function(card) {
                const nome = card.getAttribute('data-nome');
                card.style.display = nome.includes(termo) ? '' : 'none';
            });
        });
    });
</script>
@endpush