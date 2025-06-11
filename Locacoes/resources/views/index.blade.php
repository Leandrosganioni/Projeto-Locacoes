@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<style>
    .video-section {
        position: relative;
        height: 500px;
        overflow: hidden;
    }

    .video-section video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -2;
    }

    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        z-index: -1;
        background-size: cover;
        background-position: center;
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

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 video-section rounded shadow">

            <video id="heroVideo" autoplay muted playsinline></video>
            <div id="videoOverlay" class="video-overlay"></div>

            <button id="toggleMode" class="btn btn-sm btn-light fw-bold toggle-btn">Desativar vídeo</button>

            <div class="d-flex justify-content-center align-items-center h-100 text-center text-white hero-text px-3">
                <div>
                    <h1 class="fw-bold display-4 mb-3">Bem-vindo ao Eloc Locações!</h1>
                    <p class="lead fs-5 mb-4">Sistema de locações de ferramentas e maquinários</p>
                    <a href="{{ route('equipamentos.index') }}" class="btn btn-primary fw-bold px-5 py-3">Gerenciar Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const videoElement = document.getElementById('heroVideo');
    const toggleBtn = document.getElementById('toggleMode');
    const overlay = document.getElementById('videoOverlay');

    const videos = [
        "{{ asset('videos/intro1.mp4') }}",
        "{{ asset('videos/intro2.mp4') }}",
        "{{ asset('videos/intro3.mp4') }}",
        "{{ asset('videos/intro4.mp4') }}"
    ];

    let current = 0;
    let videoEnabled = true;

    function playNextVideo() {
        if (!videoEnabled) return;

        videoElement.src = videos[current];
        videoElement.load();
        videoElement.play().catch(err => console.warn("Erro ao iniciar vídeo:", err));
        current = (current + 1) % videos.length;
    }

    videoElement.addEventListener('ended', playNextVideo);
    document.addEventListener('DOMContentLoaded', playNextVideo);

    toggleBtn.addEventListener('click', () => {
        videoEnabled = !videoEnabled;

        if (!videoEnabled) {
            videoElement.pause();
            videoElement.style.display = 'none';
            overlay.style.backgroundImage = "url('{{ asset('images/fundo-locacoes.png') }}')";
            toggleBtn.textContent = 'Ativar vídeo';
        } else {
            overlay.style.backgroundImage = '';
            videoElement.style.display = 'block';
            playNextVideo();
            toggleBtn.textContent = 'Desativar vídeo';
        }
    });
</script>
@endsection
