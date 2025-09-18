@extends('layouts.app')

@section('title', 'Detalhes do Equipamento')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Detalhes do Equipamento</h1>

    <div class="card">
        <div class="card-body">
            @php
                $imgPath = public_path('images/equipamentos/' . $equipamento->imagem);
                $temImagem = $equipamento->imagem && is_file($imgPath);
            @endphp
            <div class="mb-3 text-center">
                @if($temImagem)
                    <img src="{{ asset('images/equipamentos/' . $equipamento->imagem) }}"
                         alt="Imagem do Equipamento"
                         class="img-thumbnail"
                         style="max-width: 300px;">
                @else
                    <span class="text-muted">Sem imagem</span>
                @endif
            </div>

            <h5 class="card-title">{{ $equipamento->nome }}</h5>
            <p class="card-text"><strong>Tipo:</strong> {{ $equipamento->tipo }}</p>
            <p class="card-text"><strong>Quantidade:</strong> {{ $equipamento->quantidade }}</p>
            <p class="card-text"><strong>Descrição Técnica:</strong> {{ $equipamento->descricao_tecnica }}</p>
            <p class="card-text"><strong>Informações de Manutenção:</strong> {{ $equipamento->informacoes_manutencao }}</p>

            <div class="mt-4">
                <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-warning me-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
@endpush
