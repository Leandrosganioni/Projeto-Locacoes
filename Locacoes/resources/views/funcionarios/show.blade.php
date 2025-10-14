@extends('layouts.app')

@section('title', 'Detalhes do Funcionário')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Detalhes do Funcionário</h2>
        <div>
            <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-outline-warning me-2">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4">
        <h5 class="fw-semibold mb-3 border-bottom pb-2">Informações do Funcionário</h5>
        <dl class="row mb-0">
            <dt class="col-sm-3">Nome</dt>
            <dd class="col-sm-9">{{ $funcionario->nome }}</dd>

            <dt class="col-sm-3">CPF</dt>
            <dd class="col-sm-9">{{ $funcionario->cpf }}</dd>

            <dt class="col-sm-3">Telefone</dt>
            <dd class="col-sm-9">{{ $funcionario->telefone }}</dd>

        </dl>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush