@extends('layouts.app')

@section('title', 'Página Inicial')

@push('styles')
<style>
    .equipamento-card {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .equipamento-card-img-top {
        width: 100%;
        height: 200px;
        object-fit: contain;
        padding: 10px;
    }

    .equipamento-card-body {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="container py-5">


    <div class="bg-white shadow rounded p-4 mb-4">
        <h2 class="h4 fw-semibold mb-3">Bem-vindo(a), {{ Auth::user()->name }}!</h2>
        <p class="mb-0 text-muted">Aqui pode consultar os nossos equipamentos disponíveis para locação e acompanhar os seus pedidos.</p>
    </div>


    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body p-4 text-center bg-light rounded">
            <h5 class="fw-semibold">Precisa de ajuda ou quer fazer uma nova locação?</h5>
            <p class="mb-3">Entre em contato conosco diretamente pelo WhatsApp!</p>

            @php

            $numeroTelefone = '5518981541548';

            $mensagem = rawurlencode('Olá! Gostaria de mais informações sobre os equipamentos para locação.');

            $linkWhatsApp = "https://wa.me/{$numeroTelefone}?text={$mensagem}";
            @endphp

            <a href="{{ $linkWhatsApp }}" target="_blank" class="btn btn-success btn-lg">
                <i class="bi bi-whatsapp me-2"></i> Falar Conosco (18) 98154-1548
            </a>
        </div>
    </div>




    <div class="bg-white shadow rounded p-4">
        <h3 class="fw-semibold mb-4 border-bottom pb-2">Equipamentos Disponíveis</h3>

        <div class="row g-4">
            @forelse($equipamentosDisponiveis as $equipamento)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card shadow-sm equipamento-card border-0">

                    <img src="{{ asset('images/equipamentos/' . $equipamento->imagem) }}" class="card-img-top equipamento-card-img-top" alt="{{ $equipamento->nome }}">


                    <div class="card-body equipamento-card-body">

                        <h5 class="card-title h6 fw-semibold">{{ $equipamento->nome }}</h5>


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