@extends('layouts.app')

@section('title', 'Adicionar Cliente')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <h2 class="mb-4 fw-semibold">Adicionar Novo Cliente</h2>

        {{-- Exibe erros de validação gerais, se houver --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            
            {{-- Campos existentes --}}
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                {{-- old('nome') mantém o valor digitado se a validação falhar --}}
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required>
                {{-- Exibe erro específico para o campo nome --}}
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control @error('cpf') is-invalid @enderror" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required>
                     @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone') }}" required>
                     @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <textarea class="form-control @error('endereco') is-invalid @enderror" id="endereco" name="endereco" rows="3" required>{{ old('endereco') }}</textarea>
                 @error('endereco')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- --- Documentação (Novos Campos de Autenticação) --- --}}
            {{-- Adicionamos uma seção para os dados de login do cliente --}}
            <hr class="my-4">
            <h5 class="mb-3 fw-semibold">Dados de Acesso (Login)</h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    {{-- Campo para o e-mail que será usado para login --}}
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                     @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-6">
                    <label for="password" class="form-label">Senha</label>
                    {{-- Campo para a senha --}}
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                     @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    {{-- Campo para confirmação da senha. O nome 'password_confirmation' é importante para a validação do Laravel --}}
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    {{-- Não precisa de @error aqui, o Laravel valida a confirmação automaticamente --}}
                </div>
            </div>
            
            {{-- Botões de ação --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                 <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                 </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Salvar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

