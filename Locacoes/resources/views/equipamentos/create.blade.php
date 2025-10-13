@extends('layouts.app')

@section('title', 'Adicionar Equipamento')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Adicionar Novo Equipamento</h1>

    <form method="POST" action="{{ route('equipamentos.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" required>
                @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-6">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo" value="{{ old('tipo') }}" required>
                @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label for="daily_rate" class="form-label">Diária (R$)</label>
                <input type="number" step="0.01" min="0" class="form-control" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', 0) }}" required>
                @error('daily_rate') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label for="quantidade_total" class="form-label">Quantidade (Total)</label>
                <input type="number" class="form-control" id="quantidade_total" name="quantidade_total" min="0" value="{{ old('quantidade_total', 0) }}" required>
                <small class="text-muted">A disponibilidade inicial será igual ao total.</small>
                @error('quantidade_total') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-md-4">
                <label for="imagem" class="form-label">Imagem (opcional)</label>
                <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                @error('imagem') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="descricao_tecnica" class="form-label">Descrição Técnica</label>
                <textarea class="form-control" id="descricao_tecnica" name="descricao_tecnica" rows="3" required>{{ old('descricao_tecnica') }}</textarea>
                @error('descricao_tecnica') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12">
                <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
                <textarea class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" rows="3" required>{{ old('informacoes_manutencao') }}</textarea>
                @error('informacoes_manutencao') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
@endsection
