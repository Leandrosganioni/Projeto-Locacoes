@extends('layouts.app')

@section('title', 'Registro de Quebras e Devoluções')

@section('content')
<div class="container">
    <div class="card shadow-lg p-4">
        <h1 class="card-title text-center text-primary mb-4">
            <i class="bi bi-search"></i> Buscar Pedido para Ocorrência
        </h1>
        <p class="text-center text-muted">Digite o número do Pedido (ID) para registrar uma quebra ou devolução.</p>

        <form action="{{ route('quebras.create', ['pedido_id' => '__ID__']) }}" method="GET" id="search-form">
            <div class="input-group mb-3">
                <input type="number" id="pedido_id_input" class="form-control form-control-lg" placeholder="Número do Pedido (Ex: 103)" aria-label="Número do Pedido" required min="1">
                <button class="btn btn-primary btn-lg" type="submit">
                    <i class="bi bi-arrow-right-circle"></i> Continuar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('search-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const input = document.getElementById('pedido_id_input');
        const pedidoId = input.value;
        
        if (pedidoId) {
            // Constrói a URL dinamicamente e redireciona
            const urlTemplate = '{{ route('quebras.create', ['pedido_id' => '__ID__']) }}';
            const finalUrl = urlTemplate.replace('__ID__', pedidoId);
            window.location.href = finalUrl;
        }
    });
</script>
@endsection
```