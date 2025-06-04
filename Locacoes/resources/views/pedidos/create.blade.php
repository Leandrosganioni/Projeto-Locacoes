@extends('layouts.app')

@section('title', 'Novo Pedido')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Novo Pedido</h1>

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

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Cliente</label>
                <select name="cliente_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Funcionário</label>
                <select name="funcionario_id" class="form-select" required>
                    <option value="">Selecione</option>
                    @foreach($funcionarios as $funcionario)
                        <option value="{{ $funcionario->id }}">{{ $funcionario->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Data de Entrega</label>
                <input type="date" name="data_entrega" class="form-control" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Local de Entrega</label>
                <input type="text" name="local_entrega" class="form-control" required>
            </div>
        </div>

        <h5 class="mt-4 mb-3">Equipamentos</h5>
        <div class="row row-cols-1 row-cols-md-3 g-3 mb-4">
            @foreach($produtos as $produto)
                <div class="col">
                    <div class="card equipamento-card h-100" tabindex="0" data-id="{{ $produto->id }}">
                        @if($produto->imagem)
                            <img src="{{ asset('images/equipamentos/' . $produto->imagem) }}"
                                 class="card-img-top"
                                 alt="{{ $produto->nome }}"
                                 style="height: 140px; object-fit: contain;">
                        @endif
                        <div class="card-body text-center">
                            <h6 class="card-title">{{ $produto->nome }}</h6>
                            <input type="hidden" name="produtos[]" value="">
                            <input type="number"
                                   class="form-control form-control-sm quantidade-input mt-2"
                                   placeholder="Qtd"
                                   name="quantidades[{{ $produto->id }}]"
                                   min="1"
                                   disabled>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-primary">Criar Pedido</button>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<style>
    .equipamento-card {
        cursor: pointer;
        transition: .2s;
        border: 2px solid transparent;
    }

    .equipamento-card.selected {
        border-color: #0d6efd;
        box-shadow: 0 0 8px rgba(0, 123, 255, .3);
    }
</style>

<script>
    document.querySelectorAll('.equipamento-card').forEach(card => {
        const id = card.dataset.id;
        const inputQtd = card.querySelector('.quantidade-input');
        const hidden = card.querySelector('input[type="hidden"]');

        card.addEventListener('click', e => {
            
            if (e.target.tagName === 'INPUT') return;

            card.classList.toggle('selected');
            const selecionado = card.classList.contains('selected');
            inputQtd.disabled = !selecionado;
            hidden.value = selecionado ? id : '';
            if (!selecionado) inputQtd.value = '';
        });
    });
</script>
@endsection
