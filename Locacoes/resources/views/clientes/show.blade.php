<!DOCTYPE html>
<html>
<head>
    <title>Detalhes do Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Detalhes do Cliente</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $cliente->nome }}</h5>
                <p class="card-text"><strong>CPF:</strong> {{ $cliente->cpf }}</p>
                <p class="card-text"><strong>Telefone:</strong> {{ $cliente->telefone }}</p>
                <p class="card-text"><strong>Endereço:</strong> {{ $cliente->endereco }}</p>
                
                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</body>
</html>