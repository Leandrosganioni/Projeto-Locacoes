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

        <div class="mb-3">
            <label class="form-label">Equipamentos</label>
            @foreach($produtos as $produto)
                <div class="d-flex align-items-center mb-2">
                    <input type="checkbox" class="form-check-input me-2" name="produtos[]" value="{{ $produto->id }}" id="produto_{{ $produto->id }}">
                    <label class="me-2" for="produto_{{ $produto->id }}">{{ $produto->nome }}</label>
                    <input type="number" name="quantidades[{{ $produto->id }}]" class="form-control ms-2" placeholder="Qtd" style="width: 100px;">
                </div>
            @endforeach
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
