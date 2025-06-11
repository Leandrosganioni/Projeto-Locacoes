@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Criar Novo Pedido</h1>

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

    <form method="POST" action="{{ route('pedidos.store') }}">
        @csrf

        <div class="mb-3">
            <label for="cliente_id" class="form-label">Cliente</label>
            <select class="form-select" name="cliente_id" required>
                <option value="" selected disabled>Selecione um cliente</option>
                @foreach($clientes as $cliente)
                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="funcionario_id" class="form-label">Funcionário Responsável</label>
            <select class="form-select" name="funcionario_id" required>
                <option value="" selected disabled>Selecione um funcionário</option>
                @foreach($funcionarios as $funcionario)
                <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Equipamentos</label>

            <div class="mb-3">
                <input type="text" class="form-control" id="busca-equipamento" placeholder="Buscar equipamento...">
            </div>

            <div class="row">
                @foreach($produtos as $produto)
                <div class="col-md-3 mb-4 equipamento-card" data-nome="{{ Str::slug($produto->nome) }}">
                    <div class="card h-100">
                        <img src="{{ asset('images/equipamentos/' . $produto->imagem) }}"
                            class="card-img-top"
                            alt="{{ $produto->nome }}"
                            style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">{{ $produto->nome }}</h5>
                            <p class="mb-2 text-muted">Disponível: {{ $produto->quantidade }}</p>
                            <div class="form-check mb-2">
                                <input class="form-check-input toggle-qtd" type="checkbox"
                                    value="{{ $produto->id }}"
                                    name="produtos[]"
                                    id="produto_{{ $produto->id }}">
                                <label class="form-check-label" for="produto_{{ $produto->id }}">Selecionar</label>
                            </div>
                            <input type="number"
                                name="quantidades[{{ $produto->id }}]"
                                class="form-control quantidade-input"
                                placeholder="Qtd"
                                min="1"
                                max="{{ $produto->quantidade }}"
                                style="display: none;">
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

        <div class="mb-3">
            <label for="local_entrega" class="form-label">Local de Entrega</label>
            <input type="text" class="form-control" name="local_entrega" required>
        </div>

        <div class="mb-3">
            <label for="data_entrega" class="form-label">Data de Entrega</label>
            <input type="date" class="form-control" name="data_entrega" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Pedido</button>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Mostrar/ocultar campo de quantidade ao marcar o checkbox
    document.querySelectorAll('.toggle-qtd').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const input = this.closest('.card-body').querySelector('.quantidade-input');
            input.style.display = this.checked ? 'block' : 'none';
        });
    });

    // Filtro de busca por nome
    const campoBusca = document.getElementById('busca-equipamento');
    campoBusca.addEventListener('input', function() {
        const termo = this.value.toLowerCase().trim().replace(/\s+/g, '-');
        document.querySelectorAll('.equipamento-card').forEach(card => {
            const nome = card.getAttribute('data-nome');
            card.style.display = nome.includes(termo) ? '' : 'none';
        });
    });
</script>
@endpush