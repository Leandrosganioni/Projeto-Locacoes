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

            <div class="row">
                <div class="col-md-6">
                    <p class="card-text"><strong>Tipo:</strong> {{ $equipamento->tipo }}</p>
                    <p class="card-text"><strong>Diária:</strong> R$ {{ number_format($equipamento->daily_rate, 2, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <p class="card-text"><strong>Quantidade Total:</strong> {{ $equipamento->quantidade_total }}</p>
                    <p class="card-text"><strong>Disponível:</strong> {{ $equipamento->quantidade_disponivel }}</p>
                </div>
            </div>

            <p class="card-text"><strong>Descrição Técnica:</strong> {{ $equipamento->descricao_tecnica }}</p>
            <p class="card-text"><strong>Informações de Manutenção:</strong> {{ $equipamento->informacoes_manutencao }}</p>

            <div class="mt-4">
                <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-warning me-2">
                    Editar
                </a>
                <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">
                    Voltar
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
