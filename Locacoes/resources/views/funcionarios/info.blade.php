@extends('layouts.app')

@section('title', 'Minha Conta')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Minha Conta</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $funcionario->nome }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $funcionario->email }}</p>
            <p class="card-text"><strong>Nível de Acesso:</strong> {{ $funcionario->nivel_acesso }}</p>
        </div>
    </div>
</div>
@endsection
