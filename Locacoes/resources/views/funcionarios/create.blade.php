@extends('layouts.app')

@section('title', 'Adicionar Funcionário')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <h2 class="mb-4 fw-semibold">Adicionar Novo Funcionário</h2>

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

        <form method="POST" action="{{ route('funcionarios.store') }}">
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
                <div class="col-md-6">
                    <label for="cpf" class="form-label">CPF</label>
                    <input type="text" class="form-control cpf-mask @error('cpf') is-invalid @enderror" id="cpf" name="cpf" placeholder="000.000.000-00" value="{{ old('cpf') }}" required>
                    @error('cpf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="telefone" class="form-label">Telefone</label>
                    <input type="text" class="form-control phone-mask @error('telefone') is-invalid @enderror" id="telefone" name="telefone" value="{{ old('telefone') }}" required>
                    @error('telefone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Adicionar outros campos específicos de funcionário se existirem no Model, como cargo, salario, etc. --}}
            {{-- Exemplo: --}}
            {{-- <div class="row mb-3">
                <div class="col-md-6">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text" class="form-control @error('cargo') is-invalid @enderror" id="cargo" name="cargo" value="{{ old('cargo') }}">
                    @error('cargo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="salario" class="form-label">Salário (R$)</label>
                    <input type="number" step="0.01" class="form-control @error('salario') is-invalid @enderror" id="salario" name="salario" value="{{ old('salario') }}">
                    @error('salario') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div> --}}


            {{-- --- Documentação (Novos Campos de Autenticação e Papel) --- --}}
            <hr class="my-4">
            <h5 class="mb-3 fw-semibold">Dados de Acesso e Permissão</h5>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="role" class="form-label">Papel / Permissão</label>
                    {{-- Dropdown para selecionar o papel (role) --}}
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}>Selecione o papel...</option>
                        <option value="funcionario" {{ old('role') == 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                    @error('role')
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
                    {{-- Não precisa de @error aqui, o Laravel valida a confirmação automaticamente --}}
                </div>
            </div>

            {{-- Botões de ação --}}
            <div class="mt-4 d-flex justify-content-end gap-2">
                 <a href="{{ route('funcionarios.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                 </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Salvar Funcionário
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    {{-- Importa o jQuery (se já não estiver no layout) e o Mask Plugin --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    {{-- Nosso script personalizado --}}
    <script src="{{ asset('js/custom-masks.js') }}"></script>
@endpush

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush