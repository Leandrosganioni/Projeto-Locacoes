<!DOCTYPE html>
<html>
<head>
    <title>Editar Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Editar Estoque</h1>
        
        <form method="POST" action="{{ route('equipamentos.update', $equipamento->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Equipamento</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ $equipamento->nome }}" required>
            </div>
            
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo" value="{{ $equipamento->tipo }}" required>
            </div>
            
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" value="{{ $equipamento->quantidade }}" required>
            </div>

            <div class="mb-3">
                <label for="descricao_tecnica" class="form-label">Descrição</label>
                <input type="text" class="form-control" id="descricao_tecnica" name="descricao_tecnica" value="{{ $equipamento->descricao_tecnica }}" required>
            </div>

            <div class="mb-3">
                <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
                <input type="text" class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" value="{{ $equipamento->informacoes_manutencao }}" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>