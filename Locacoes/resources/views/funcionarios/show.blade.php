@extends('layouts.app')

@section('title', 'Detalhes do Funcionário')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalhes do Funcionário</h1>
    
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $funcionario->nome }}</h5>
            <p class="card-text"><strong>CPF:</strong> {{ $funcionario->cpf }}</p>
            <p class="card-text"><strong>Telefone:</strong> {{ $funcionario->telefone }}</p>
            <p class="card-text"><strong>Endereço:</strong> {{ $funcionario->endereco }}</p>
            
            <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-warning">Editar</a>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">Voltar</a>
        </div>
    </div>
</div>
@endsection