@extends('layouts.app')

@section('title', 'Editar Equipamento')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Editar Equipamento</h1>
    
    <form method="POST" action="{{ route('equipamentos.update', $equipamento->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nome" class="form-label">Nome do Equipamento</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $equipamento->nome) }}" required>
            @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo</label>
            <input type="text" class="form-control" id="tipo" name="tipo" value="{{ old('tipo', $equipamento->tipo) }}" required>
            @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        
        <div class="mb-3">
            <label for="quantidade" class="form-label">Quantidade</label>
            <input type="number" class="form-control" id="quantidade" name="quantidade" value="{{ old('quantidade', $equipamento->quantidade) }}" min="1" required>
            @error('quantidade') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="descricao_tecnica" class="form-label">Descrição Técnica</label>
            <input type="text" class="form-control" id="descricao_tecnica" name="descricao_tecnica" value="{{ old('descricao_tecnica', $equipamento->descricao_tecnica) }}" required>
            @error('descricao_tecnica') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
            <input type="text" class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" value="{{ old('informacoes_manutencao', $equipamento->informacoes_manutencao) }}" required>
            @error('informacoes_manutencao') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="imagem" class="form-label">Nova Imagem (opcional)</label>
            <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
            @error('imagem') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        @php
            $imgPath = public_path('images/equipamentos/' . $equipamento->imagem);
            $temImagem = $equipamento->imagem && is_file($imgPath);
        @endphp
        @if($temImagem)
            <img src="{{ asset('images/equipamentos/'.$equipamento->imagem) }}" alt="Imagem atual" style="max-width:200px" class="img-thumbnail mb-3">
        @endif
        
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
