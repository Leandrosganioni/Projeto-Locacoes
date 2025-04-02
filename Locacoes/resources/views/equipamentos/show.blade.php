<!DOCTYPE html>
<html>
<head>
    <title>Detalhes dos Equipamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Detalhes dos Equipamentos</h1>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $equipamento->nome }}</h5>
                <p class="card-text"><strong>Tipo:</strong> {{ $equipamento->tipo }}</p>
                <p class="card-text"><strong>Quantidade:</strong> {{ $equipamento->quantidade }}</p>
                <p class="card-text"><strong>Descricao tecnica:</strong> {{ $equipamento->descricao_tecnica }}</p>
                
                <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-warning">Editar</a>
                <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</body>
</html>