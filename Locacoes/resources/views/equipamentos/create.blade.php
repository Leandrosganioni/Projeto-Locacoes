@extends('layouts.app')

@section('title', 'Adicionar Equipamento')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Adicionar Novo Equipamento</h1>

    <form method="POST" action="{{ route('equipamentos.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
            @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" class="form-control" id="tipo" name="tipo" value="{{ old('tipo') }}" required>
            @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" value="{{ old('quantidade', 1) }}" required>
            @error('quantidade') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="descricao_tecnica" class="form-label">Descrição Técnica</label>
            <textarea class="form-control" id="descricao_tecnica" name="descricao_tecnica" rows="3" required>{{ old('descricao_tecnica') }}</textarea>
            @error('descricao_tecnica') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
            <textarea class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" rows="3" required>{{ old('informacoes_manutencao') }}</textarea>
            @error('informacoes_manutencao') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Imagem (opcional)</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
            @error('imagem') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
