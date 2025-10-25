@extends('layouts.app')

@section('title', 'Editar Equipamento')

@section('content')
<div class="container py-5">
    <div class="bg-white shadow rounded p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-semibold">Editar Equipamento</h2>
            <a href="{{ route('equipamentos.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Voltar
            </a>
        </div>

        <form method="POST" action="{{ route('equipamentos.update', $equipamento->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do Equipamento</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $equipamento->nome) }}" required>
                    @error('nome') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo</label>
                    <input type="text" class="form-control" id="tipo" name="tipo" value="{{ old('tipo', $equipamento->tipo) }}" required>
                    @error('tipo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label for="daily_rate" class="form-label">Diária (R$)</label>
                    <input type="number" step="0.01" min="0" class="form-control" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $equipamento->daily_rate) }}" required>
                    @error('daily_rate') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label for="quantidade_total" class="form-label">Quantidade Total</label>
                    <input type="number" class="form-control" id="quantidade_total" name="quantidade_total" min="0" value="{{ old('quantidade_total', $equipamento->quantidade_total) }}" required>
                    @error('quantidade_total') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label d-block">Estoque Disponível</label>
                    <input type="number" class="form-control" value="{{ $equipamento->quantidade_disponivel }}" readonly disabled>
                    <small class="text-muted">Alterado via reservas/devoluções.</small>
                </div>

                <div class="col-12">
                    <label for="descricao_tecnica" class="form-label">Descrição Técnica</label>
                    <textarea class="form-control" id="descricao_tecnica" name="descricao_tecnica" rows="3" required>{{ old('descricao_tecnica', $equipamento->descricao_tecnica) }}</textarea>
                    @error('descricao_tecnica') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12">
                    <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
                    <textarea class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" rows="3" required>{{ old('informacoes_manutencao', $equipamento->informacoes_manutencao) }}</textarea>
                    @error('informacoes_manutencao') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="imagem" class="form-label">Substituir Imagem (opcional)</label>
                    <input type="file" class="form-control" id="imagem" name="imagem" accept="image/*">
                    @error('imagem') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Imagem Atual</label>
                    <div>
                        @if($equipamento->imagem && file_exists(public_path('images/equipamentos/' . $equipamento->imagem)))
                            <img src="{{ asset('images/equipamentos/'.$equipamento->imagem) }}" alt="Imagem atual" style="max-height: 100px;" class="img-thumbnail">
                        @else
                            <span class="text-muted">Sem imagem.</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Atualizar Equipamento</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush