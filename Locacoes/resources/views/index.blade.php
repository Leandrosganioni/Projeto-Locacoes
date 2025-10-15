@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<div class="container py-5">
    <!-- Seção Principal com Vídeo -->
    <div class="row justify-content-center">
        <div class="col-lg-11 video-section rounded shadow-lg overflow-hidden">
            <video id="heroVideo" autoplay muted loop playsinline preload="auto"></video>
            <div id="videoOverlay" class="video-overlay"></div>

            <button id="toggleMode" class="btn btn-sm btn-light fw-bold toggle-btn">
                <i class="bi bi-camera-video-off-fill"></i> Desativar vídeo
            </button>

            <div class="d-flex justify-content-center align-items-center h-100 text-center text-white hero-text px-3">
                <div>
                    <h1 class="fw-bold display-4 mb-3">Bem-vindo ao Eloc Locações!</h1>
                    <p class="lead fs-5 mb-4">Seu sistema completo para gestão de locação de ferramentas e maquinários.</p>
                    <a href="{{ route('pedidos.index') }}" class="btn btn-primary fw-bold px-5 py-3">
                        <i class="bi bi-box-seam me-2"></i> Gerenciar Pedidos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Navegação e Métricas -->
    <div class="row mt-5 g-4">
        <div class="col-md-4">
            <div class="bg-white shadow-sm p-4 rounded h-100 d-flex flex-column">
                <i class="bi bi-people-fill display-4 text-primary mb-3"></i>
                <h4 class="fw-semibold">Clientes</h4>
                <p class="text-muted">Você tem <strong class="text-dark">{{ $totalClientes ?? '0' }}</strong> clientes cadastrados.</p>
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-primary mt-auto">Gerenciar Clientes</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white shadow-sm p-4 rounded h-100 d-flex flex-column">
                <i class="bi bi-tools display-4 text-primary mb-3"></i>
                <h4 class="fw-semibold">Equipamentos</h4>
                <p class="text-muted">Seu inventário possui <strong class="text-dark">{{ $totalEquipamentos ?? '0' }}</strong> itens.</p>
                <a href="{{ route('equipamentos.index') }}" class="btn btn-outline-primary mt-auto">Ver Estoque</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="bg-white shadow-sm p-4 rounded h-100 d-flex flex-column">
                <i class="bi bi-journal-text display-4 text-primary mb-3"></i>
                <h4 class="fw-semibold">Pedidos</h4>
                <p class="text-muted">Um total de <strong class="text-dark">{{ $totalPedidos ?? '0' }}</strong> pedidos foram realizados.</p>
                <a href="{{ route('pedidos.index') }}" class="btn btn-outline-primary mt-auto">Ver Pedidos</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Pré-carrega a imagem de fundo para evitar "piscar" na troca --}}
<link rel="preload" as="image" href="{{ asset('images/fundo-locacoes.png') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .video-section {
        position: relative;
        height: 500px;
        background-size: cover;
        background-position: center;
    }
    .video-section video {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: translate(-50%, -50%);
        z-index: -2;
        transition: opacity 0.3s ease-in-out;
    }
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: -1;
    }
    .hero-text {
        position: relative;
        z-index: 1;
    }
    .toggle-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 2;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoElement = document.getElementById('heroVideo');
    const toggleBtn = document.getElementById('toggleMode');
    const videoSection = document.querySelector('.video-section');
    const videoOverlay = document.getElementById('videoOverlay');

    const videos = [
        "{{ asset('videos/intro1.mp4') }}",
        "{{ asset('videos/intro2.mp4') }}",
        "{{ asset('videos/intro3.mp4') }}",
        "{{ asset('videos/intro4.mp4') }}"
    ];
    let currentVideoIndex = Math.floor(Math.random() * videos.length);

    function playNextVideo() {
        if (localStorage.getItem('videoEnabled') === 'false') return;
        
        currentVideoIndex = (currentVideoIndex + 1) % videos.length;
        videoElement.src = videos[currentVideoIndex];
        videoElement.play().catch(err => console.warn("Erro ao iniciar vídeo:", err));
    }

    function setVideoState(enabled) {
        if (enabled) {
            localStorage.setItem('videoEnabled', 'true');
            videoSection.style.backgroundImage = '';
            videoOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.6)';
            videoElement.style.display = 'block';
            toggleBtn.innerHTML = '<i class="bi bi-camera-video-off-fill"></i> Desativar vídeo';
            playNextVideo();
        } else {
            localStorage.setItem('videoEnabled', 'false');
            videoElement.pause();
            videoElement.style.display = 'none';
            videoSection.style.backgroundImage = "url('{{ asset('images/fundo-locacoes.png') }}')";
            videoOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            toggleBtn.innerHTML = '<i class="bi bi-camera-video-fill"></i> Ativar vídeo';
        }
    }

    videoElement.addEventListener('ended', playNextVideo);

    toggleBtn.addEventListener('click', () => {
        const isEnabled = localStorage.getItem('videoEnabled') !== 'false';
        setVideoState(!isEnabled);
    });
    
    setVideoState(localStorage.getItem('videoEnabled') !== 'false');
});
</script>
@endpush