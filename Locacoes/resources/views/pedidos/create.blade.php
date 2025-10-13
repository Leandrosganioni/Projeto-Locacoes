@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center fw-bold">Novo Pedido</h2>

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

    <form id="form-pedido" method="POST" action="{{ route('pedidos.store') }}">
        @csrf

        <div class="bg-white shadow rounded p-4 mb-5">
            <h5 class="mb-4 fw-semibold border-bottom pb-2">Informações do Pedido</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Cliente</label>
                    <select class="form-select select2" name="cliente_id" required>
                        <option value="" selected disabled>Pesquisar...</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Funcionário Responsável</label>
                    <select class="form-select select2" name="funcionario_id" required>
                        <option value="" selected disabled>Pesquisar...</option>
                        @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Local de Entrega</label>
                    <input type="text" class="form-control" name="local_entrega" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Data de Entrega</label>
                    <input type="date" class="form-control" name="data_entrega" required>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded p-4 mb-5">
            <h5 class="mb-4 fw-semibold border-bottom pb-2">Equipamentos Disponíveis</h5>

            <div class="mb-4">
                <input type="text" class="form-control" id="busca-equipamento" placeholder="Buscar equipamento...">
            </div>

            <div class="row">
                @foreach($produtos as $produto)
                <div class="col-md-3 col-sm-6 mb-4 equipamento-card" data-nome="{{ Str::slug($produto->nome) }}">
                    <div class="card h-100 border-0 shadow-sm rounded">
                        <img src="{{ asset('images/equipamentos/' . $produto->imagem) }}"
                            class="card-img-top rounded-top"
                            alt="{{ $produto->nome }}"
                            style="height: 170px; object-fit: cover;">
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h6 class="fw-bold mb-1">{{ $produto->nome }}</h6>
                            <small class="text-muted d-block mb-2">
                                Disponível: {{ $produto->quantidade_disponivel }}
                                · Diária: R$ {{ number_format($produto->daily_rate, 2, ',', '.') }}
                            </small>

                            <div class="form-check small mb-1">
                                <input class="form-check-input toggle-qtd" type="checkbox"
                                    value="{{ $produto->id }}"
                                    name="produtos[]"
                                    id="produto_{{ $produto->id }}">
                                <label class="form-check-label" for="produto_{{ $produto->id }}">Selecionar</label>
                            </div>
                            <input type="number"
                                name="quantidades[{{ $produto->id }}]"
                                class="form-control form-control-sm quantidade-input mt-2"
                                placeholder="Qtd"
                                min="1"
                                max="{{ $produto->quantidade_disponivel }}"
                                style="display: none;" disabled>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Salvar Pedido</button>
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

        document.querySelectorAll('.toggle-qtd').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const input = this.closest('.card-body').querySelector('.quantidade-input');
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


        document.getElementById('busca-equipamento').addEventListener('input', function() {
            const termo = this.value.toLowerCase().trim().replace(/\s+/g, '-');
            document.querySelectorAll('.equipamento-card').forEach(card => {
                const nome = card.getAttribute('data-nome');
                card.style.display = nome.includes(termo) ? '' : 'none';
            });
        });


        document.getElementById('form-pedido').addEventListener('submit', function(e) {
            let ok = true,
                msg = [];
            document.querySelectorAll('.equipamento-card').forEach(card => {
                const chk = card.querySelector('.toggle-qtd');
                const qty = card.querySelector('.quantidade-input');
                const max = parseInt(qty?.getAttribute('max') || '0', 10);
                const nome = card.querySelector('.fw-bold, h6, strong')?.textContent?.trim() || 'Equipamento';

                if (chk && chk.checked) {
                    const val = parseInt(qty.value || '0', 10);
                    if (!val || val < 1) {
                        ok = false;
                        msg.push(`${nome}: informe a quantidade.`);
                    } else if (val > max) {
                        ok = false;
                        msg.push(`${nome}: acima do disponível (${max}).`);
                    }
                }
            });
            if (!ok) {
                e.preventDefault();
                alert(msg.join('\n'));
            }
        });

    });
</script>
@endpush