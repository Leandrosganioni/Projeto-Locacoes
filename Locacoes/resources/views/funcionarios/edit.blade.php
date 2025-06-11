@extends('layouts.app') {{-- Ou o seu layout principal --}}

@section('title', 'Editar Funcionário')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    {{-- Usando o helper de tradução para um título mais dinâmico --}}
                    <h4>Editar Funcionário: {{ $funcionario->nome }}</h4>
                </div>
                <div class="card-body">
                    {{-- Formulário apontando para a rota de update, com o método PUT --}}
                    <form action="{{ route('funcionarios.update', $funcionario->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Nome --}}
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" name="nome" id="nome" class="form-control" value="{{ old('nome', $funcionario->nome) }}" required>
                        </div>

                        {{-- CPF --}}
                        <div class="mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-control" value="{{ old('cpf', $funcionario->cpf) }}" required>
                        </div>

                        {{-- Telefone --}}
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="telefone" class="form-control" value="{{ old('telefone', $funcionario->telefone) }}" required>
                        </div>

                        <hr>
                        <p class="text-muted">Ajustes de Acesso ao Sistema</p>

                        {{-- Nível de Acesso --}}
                        <div class="mb-3">
                            <label for="nivel_acesso" class="form-label">Nível de Acesso</label>
                            <select name="nivel_acesso" id="nivel_acesso" class="form-select">
                                {{-- Lógica para deixar a opção correta selecionada --}}
                                <option value="FUNCIONARIO" {{ old('nivel_acesso', $funcionario->nivel_acesso) == 'FUNCIONARIO' ? 'selected' : '' }}>
                                    Apenas Funcionário (Sem acesso ao sistema)
                                </option>
                                <option value="COLABORADOR" {{ old('nivel_acesso', $funcionario->nivel_acesso) == 'COLABORADOR' ? 'selected' : '' }}>
                                    Colaborador
                                </option>
                                <option value="ADMINISTRADOR" {{ old('nivel_acesso', $funcionario->nivel_acesso) == 'ADMINISTRADOR' ? 'selected' : '' }}>
                                    Administrador
                                </option>
                            </select>
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail (para login)</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $funcionario->email) }}">
                        </div>

                        {{-- Senha --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Nova Senha</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Deixe em branco para não alterar">
                            <div class="form-text">Preencha este campo apenas se desejar alterar a senha atual.</div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('funcionarios.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Atualizar Funcionário</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection