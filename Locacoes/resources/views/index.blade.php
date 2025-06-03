@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="position-relative rounded shadow overflow-hidden" style="height: 500px;">
                <div class="w-100 h-100" style="
                    background-image: url('/images/fundo-locacoes.png');
                    background-size: cover;
                    background-position: center;
                "></div>

                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center bg-dark bg-opacity-50 text-white text-center p-4">
                    <h1 class="fw-bold display-4 mb-3">Bem-vindo ao Eloc Locações!</h1>
                    <p class="lead">Uma solução completa para seu negócio</p>
                    <a href="{{ route('equipamentos.index') }}" class="btn btn-primary fw-bold px-4 py-2 mt-3">Bora lá</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
