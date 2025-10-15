@extends('layouts.app')

@section('title', 'Adicionar Cliente')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <h2 class="mb-4 fw-semibold">Adicionar Novo Cliente</h2>
        
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
            </div>
            
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>
            
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <textarea class="form-control" id="endereco" name="endereco" rows="3" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i> Salvar
            </button>
            <a href="{{ route('clientes.index') }}" class="btn btn-light">
                <i class="bi bi-x-circle me-1"></i> Cancelar
            </a>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush