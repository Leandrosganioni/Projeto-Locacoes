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
                <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome') }}" required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="row mb-3">
                
                {{-- --- A CORREÇÃO ESTÁ AQUI --- --}}
                {{-- O nome do campo (name) deve ser 'cpf_cnpj' para corresponder ao Controller e ao Banco de Dados --}}
                <div class="col-md-6">
                    <label for="cpf_cnpj" class="form-label">CPF/CNPJ</label>
                    <input type="text" class="form-control @error('cpf_cnpj') is-invalid @enderror" id="cpf_cnpj" name="cpf_cnpj" placeholder="000.000.000-00 ou 00.000.000/0000-00" value="{{ old('cpf_cnpj') }}" required>
                     @error('cpf_cnpj')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                {{-- --- FIM DA CORREÇÃO --- --}}

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

            <hr class="my-4">
            <h5 class="mb-3 fw-semibold">Dados de Acesso (Login)</h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                     @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-6">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                     @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                 <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
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