@extends('layouts.app')

@section('title', 'Central de Relatórios')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <h2 class="fw-semibold mb-4">Central de Relatórios</h2>
        <p class="text-muted mb-4">Selecione um dos relatórios disponíveis para visualização.</p>

        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('relatorios.estoque') }}" class="text-decoration-none">
                    <div class="card h-100 shadow-sm border-0 card-hover">
                        <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                            <h5 class="card-title text-primary"><i class="bi bi-boxes fs-2 mb-2"></i></h5>
                            <h6 class="card-subtitle mb-2 fw-bold">Relação de Estoque</h6>
                            <p class="card-text small text-muted">Gráfico e tabela da situação atual do estoque.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="#" class="text-decoration-none pe-none" tabindex="-1" aria-disabled="true">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                            <h5 class="card-title text-muted"><i class="bi bi-graph-up-arrow fs-2 mb-2"></i></h5>
                            <h6 class="card-subtitle mb-2 fw-bold text-muted">Relatório Financeiro</h6>
                            <p class="card-text small text-muted">(Em breve)</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                 <a href="#" class="text-decoration-none pe-none" tabindex="-1" aria-disabled="true">
                    <div class="card h-100 border-0 bg-light">
                        <div class="card-body text-center p-4 d-flex flex-column justify-content-center">
                            <h5 class="card-title text-muted"><i class="bi bi-person-lines-fill fs-2 mb-2"></i></h5>
                            <h6 class="card-subtitle mb-2 fw-bold text-muted">Relatório de Clientes</h6>
                            <p class="card-text small text-muted">(Em breve)</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<style>

.card-hover {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}
.card-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
@endsection