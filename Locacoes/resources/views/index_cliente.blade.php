@extends('layouts.app')

@section('title', 'Página Inicial')

@push('styles')
<style>
    /* Estilo para garantir que os cards tenham a mesma altura */
    .equipamento-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .equipamento-card-img-top {
        width: 100%;
        height: 200px; /* Altura fixa para a imagem */
        object-fit: contain; /* 'contain' para mostrar a imagem inteira sem distorcer */
        padding: 10px;
    }
    .equipamento-card-body {
        flex-grow: 1; /* Faz o corpo do card crescer para preencher o espaço */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    
    {{-- Mensagem de Boas-vindas --}}
    <div class="bg-white shadow rounded p-4 mb-4">
        <h2 class="h4 fw-semibold mb-3">Bem-vindo(a), {{ Auth::user()->name }}!</h2>
        <p class="mb-0 text-muted">Aqui pode consultar os nossos equipamentos disponíveis para locação e acompanhar os seus pedidos.</p>
    </div>

    {{-- Botão de Contacto (WhatsApp) --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4 text-center bg-light rounded">
            <h5 class="fw-semibold">Precisa de ajuda ou quer fazer uma nova locação?</h5>
            <p class="mb-3">Entre em contacto connosco diretamente pelo WhatsApp!</p>
            
            <a href="https://wa.me/55119XXXXXXXX" target="_blank" class="btn btn-success btn-lg">
                <i class="bi bi-whatsapp me-2"></i> Falar Connosco
            </a>
        </div>
    </div>

    {{-- Catálogo de Equipamentos --}}
    <div class="bg-white shadow rounded p-4">
        <h3 class="fw-semibold mb-4 border-bottom pb-2">Equipamentos Disponíveis</h3>

        <div class="row g-4">
            @forelse($equipamentosDisponiveis as $equipamento)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card shadow-sm equipamento-card border-0">
                        {{-- Imagem do Equipamento --}}
                        <img src="{{ $equipamento->image_url }}" class="card-img-top equipamento-card-img-top" alt="{{ $equipamento->nome }}">
                        
                        <div class="card-body equipamento-card-body">
                            {{-- Nome do Equipamento --}}
                            <h5 class="card-title h6 fw-semibold">{{ $equipamento->nome }}</h5>
                            
                            {{-- Preço (Diária) --}}
                            <div>
                                <span class="text-muted small">Diária a partir de</span>
                                <p class="card-text fs-5 fw-bold text-primary mb-0">
                                    R$ {{ number_format($equipamento->daily_rate, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center text-muted">Nenhum equipamento disponível no momento.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection