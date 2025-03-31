<!DOCTYPE html>
<html>
<head>
    <title>Adicionar Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Adicionar Novo Funcionário</h1>
        
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
                <label for="cargo" class="form-label">Cargo</label>
                <select class="form-control" id="cargo" name="cargo" required>
                    <option value="">Selecione um cargo</option>
                    <option value="Administrativo">Administrativo</option>
                    <option value="Vendas">Setor de Vendas</option>
                    <option value="RH">Recursos Humanos</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Salvar</button>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>