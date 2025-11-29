@extends('layouts.app')

@section('title', 'Editar Funcionário')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <h2 class="mb-4 fw-semibold">Editar Funcionário</h2>
        
        <form method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $funcionario->nome) }}" required>
            </div>
            
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                {{-- Adicionada classe 'cpf-mask' --}}
                <input type="text" class="form-control cpf-mask" id="cpf" name="cpf" value="{{ old('cpf', $funcionario->cpf) }}" required>
            </div>
            
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                {{-- Adicionada classe 'phone-mask' --}}
                <input type="text" class="form-control phone-mask" id="telefone" name="telefone" value="{{ old('telefone', $funcionario->telefone) }}" required>
            </div>
            
            {{-- Adicione outros campos se necessário (cargo, salario, etc) conforme seu Model --}}
            
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Atualizar
            </button>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-light">
                <i class="bi bi-x-circle me-1"></i> Cancelar
            </a>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

{{-- Scripts de Máscara --}}
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="{{ asset('js/custom-masks.js') }}"></script>
@endpush