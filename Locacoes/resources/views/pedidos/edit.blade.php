@extends('layouts.app')

@section('title', 'Editar Pedido')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Editar Pedido</h1>

    <form method="POST" action="{{ route('pedidos.update', $pedido->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select class="form-select" name="cliente_id" required>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}" {{ $cliente->id == $pedido->cliente_id ? 'selected' : '' }}>
                        {{ $cliente->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Funcionário</label>
            <select class="form-select" name="funcionario_id" required>
                @foreach($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}" {{ $funcionario->id == $pedido->funcionario_id ? 'selected' : '' }}>
                        {{ $funcionario->nome }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Produtos</label>
            @foreach($produtos as $produto)
                @php
                    $quantidade = optional($pedido->produtos->find($produto->id))->pivot->quantidade;
                @endphp
                <div class="d-flex align-items-center mb-2">
                    <input type="checkbox" class="form-check-input me-2" name="produtos[]" value="{{ $produto->id }}"
                        {{ $quantidade ? 'checked' : '' }}>
                    <label class="me-2">{{ $produto->nome }}</label>
                    <input type="number" name="quantidades[]" class="form-control ms-2" placeholder="Quantidade"
                        value="{{ $quantidade }}" style="width: 120px;">
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label class="form-label">Local de Entrega</label>
            <input type="text" class="form-control" name="local_entrega" value="{{ $pedido->local_entrega }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Data de Entrega</label>
            <input type="date" class="form-control" name="data_entrega" value="{{ $pedido->data_entrega }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Pedido</button>
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
