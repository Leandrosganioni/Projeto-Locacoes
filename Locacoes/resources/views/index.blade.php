@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 position-relative rounded shadow overflow-hidden" style="height: 500px;">
            
            <video id="heroVideo" class="w-100 h-100 object-fit-cover" autoplay muted playsinline></video>

            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-dark bg-opacity-50 text-white text-center p-4">
                <h1 class="fw-bold display-4 mb-3">Bem-vindo ao Eloc Locações!</h1>
                <p class="lead">Uma solução completa para seu negócio</p>
                <a href="{{ route('equipamentos.index') }}" class="btn btn-primary fw-bold px-4 py-2 mt-3">Bora lá</a>
            </div>
        </div>
    </div>
</div>

<script>
    const videoElement = document.getElementById('heroVideo');

    const videos = [
        "{{ asset('videos/intro1.mp4') }}",
        "{{ asset('videos/intro2.mp4') }}",
        "{{ asset('videos/intro3.mp4') }}",
        "{{ asset('videos/intro3.mp4') }}"
    ];

    let current = 0;

    function playNextVideo() {
        videoElement.src = videos[current];
        videoElement.load();
        videoElement.play().catch(err => console.warn("Erro ao iniciar vídeo:", err));
        current = (current + 1) % videos.length;
    }

    videoElement.addEventListener('ended', playNextVideo);
    document.addEventListener('DOMContentLoaded', playNextVideo);
</script>
@endsection
