@extends('layouts.app')

@section('title', 'Registro de Quebras e Devoluções')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Registrar Ocorrência</h1>
    </div>
    
    <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Equipamentos</h2>
        <a href="{{ route('quebras.relatorio') }}" class="btn btn-outline-warning">
            <i class="bi bi-file-earmark-bar-graph"></i> Ver Relatório de Quebras
        </a>
    </div>
    
    {{-- A tabela de listagem de equipamentos deve vir abaixo daqui --}}
    <table class="table table-striped">
        {{-- ... código da tabela ... --}}
    </table>
    </div>

    <div class="table-responsive">
        <div class="card shadow-sm">
            <div class="card-header table-dark">
                <h5 class="mb-0 text-white">Buscar Pedido</h5>
            </div>
            <div class="card-body p-4">
                <h1 class="card-title text-center text-primary mb-4">
                    <i class="bi bi-search"></i> Buscar Pedido para Ocorrência
                </h1>
                <p class="text-center text-muted">Digite o número do Pedido (ID) para registrar uma quebra ou devolução.</p>
        
                {{-- --- CORREÇÃO AQUI (Parte 1) --- --}}
                {{-- 
                    Passamos a URL para um atributo 'data-url-template'.
                    O HTML não se importa com a sintaxe do Blade dentro das aspas.
                --}}
                <form action="{{ route('quebras.create', ['pedido_id' => '__ID__']) }}" 
                      method="GET" 
                      id="search-form"
                      data-url-template="{{ route('quebras.create', ['pedido_id' => '__ID__']) }}">
                
                    <div class="input-group mb-3">
                        <input type="number" id="pedido_id_input" class="form-control form-control-lg" placeholder="Número do Pedido (Ex: 1)" aria-label="Número do Pedido" required min="1">
                        <button class="btn btn-primary btn-lg" type="submit">
                            <i class="bi bi-arrow-right-circle"></i> Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        

        const form = e.target; 
        
        const input = document.getElementById('pedido_id_input');
        const pedidoId = input.value;
        
        if (pedidoId) {
            const urlTemplate = form.dataset.urlTemplate;

            const finalUrl = urlTemplate.replace('__ID__', pedidoId);
            window.location.href = finalUrl;
        }
    });
</script>
@endpush