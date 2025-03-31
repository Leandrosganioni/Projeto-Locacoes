<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Funcionario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Funcionario</h1>
        
        <form method="POST" action="{{ route('funcionarios.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" placeholder="000.000.000-00" required>
            </div>
            
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>