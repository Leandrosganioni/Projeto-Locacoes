<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Estoque</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Equipamento</h1>
        
        <form method="POST" action="{{ route('equipamentos.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Equipamento</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            
            <div class="mb-3">
                <label for="tipo" class="form-label">Tipo</label>
                <input type="text" class="form-control" id="tipo" name="tipo" required>
            </div>
            
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" required>
            </div>

            <div class="mb-3">
                <label for="descriocao_tecnica" class="form-label">Descrição</label>
                <input type="text" class="form-control" id="descricao_tecnica" name="descricao_tecnica" required>
            </div>

            <div class="mb-3">
                <label for="informacoes_manutencao" class="form-label">Informações de Manutenção</label>
                <input type="text" class="form-control" id="informacoes_manutencao" name="informacoes_manutencao" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('equipamentos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>