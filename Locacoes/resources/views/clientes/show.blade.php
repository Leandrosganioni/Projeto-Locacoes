@extends('layouts.app')

@section('title', 'Detalhes do Cliente')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalhes do Cliente</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $cliente->nome }}</h5>
            <p class="card-text"><strong>CPF:</strong> {{ $cliente->cpf }}</p>
            <p class="card-text"><strong>Telefone:</strong> {{ $cliente->telefone }}</p>
            <p class="card-text"><strong>Endereço:</strong> {{ $cliente->endereco }}</p>
            
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">Editar</a>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection