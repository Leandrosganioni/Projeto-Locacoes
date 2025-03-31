<!DOCTYPE html>
<html>
<head>
    <title>Editar Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Editar Funcionário</h1>
        
        <form method="POST" action="{{ route('funcionarios.update', $funcionario->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ $funcionario->nome }}" required>
            </div>
            
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" value="{{ $funcionario->cpf }}" required>
            </div>
            
            <div class="mb-3">
                <label for="cargo" class="form-label">Cargo</label>
                <select class="form-control" id="cargo" name="cargo" required>
                    <option value="Administrativo" {{ $funcionario->cargo == 'Administrativo' ? 'selected' : '' }}>Administrativo</option>
                    <option value="Vendas" {{ $funcionario->cargo == 'Vendas' ? 'selected' : '' }}>Setor de Vendas</option>
                    <option value="RH" {{ $funcionario->cargo == 'RH' ? 'selected' : '' }}>Recursos Humanos</option>
                    <option value="Marketing" {{ $funcionario->cargo == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Atualizar</button>
            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</body>
</html>