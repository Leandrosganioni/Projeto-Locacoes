@extends('layouts.app')

@section('title', 'Detalhes do Equipamento')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <h2 class="mb-0 fw-semibold">Detalhes do Equipamento</h2>
            <a href="{{ route('equipamentos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar à Lista
            </a>
        </div>

        <div class="row g-5">
            <div class="col-md-5 col-lg-4">
                @if($equipamento->imagem && file_exists(public_path('images/equipamentos/' . $equipamento->imagem)))
                    <img src="{{ asset('images/equipamentos/' . $equipamento->imagem) }}"
                         alt="Imagem do {{ $equipamento->nome }}"
                         class="img-fluid rounded shadow-sm">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light rounded text-muted" style="height: 250px;">
                        <span>Sem imagem</span>
                    </div>
                @endif
            </div>

            <div class="col-md-7 col-lg-8">
                <h3 class="fw-bold">{{ $equipamento->nome }}</h3>
                <p class="text-muted fs-5 mb-3">{{ $equipamento->tipo }}</p>

                <dl class="row">
                    <dt class="col-sm-4">Valor da Diária</dt>
                    <dd class="col-sm-8">R$ {{ number_format($equipamento->daily_rate, 2, ',', '.') }}</dd>

                    <dt class="col-sm-4">Estoque Disponível</dt>
                    <dd class="col-sm-8">{{ $equipamento->quantidade_disponivel }}</dd>

                    <dt class="col-sm-4">Estoque Total</dt>
                    <dd class="col-sm-8">{{ $equipamento->quantidade_total }}</dd>
                </dl>

                <hr class="my-4">

                <div>
                    <h5 class="fw-semibold">Descrição Técnica</h5>
                    <p>{{ $equipamento->descricao_tecnica }}</p>
                </div>

                <div class="mt-3">
                    <h5 class="fw-semibold">Informações de Manutenção</h5>
                    <p>{{ $equipamento->informacoes_manutencao }}</p>
                </div>
            </div>
        </div>

        <div class="mt-4 border-top pt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Editar
            </a>
            <form action="{{ route('equipamentos.destroy', $equipamento->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Deseja realmente excluir este equipamento?')">
                    <i class="bi bi-trash me-1"></i> Excluir
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush