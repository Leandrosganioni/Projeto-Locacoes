@extends('layouts.app')

@section('title', 'Editar Cliente')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Editar Cliente</h1>
    
    <form method="POST" action="{{ route('clientes.update', $cliente->id) }}">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nome" class="form-label">Nome Completo</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $cliente->nome) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="cpf" class="form-label">CPF</label>
            <input type="text" class="form-control" id="cpf" name="cpf" value="{{ old('cpf', $cliente->cpf) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="telefone" class="form-label">Telefone</label>
            <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" required>
        </div>
        
        <div class="mb-3">
            <label for="endereco" class="form-label">Endereço</label>
            <textarea class="form-control" id="endereco" name="endereco" rows="3" required>{{ old('endereco', $cliente->endereco) }}</textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection