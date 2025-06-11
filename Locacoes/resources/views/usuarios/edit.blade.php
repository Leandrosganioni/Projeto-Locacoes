@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Usuário</h2>

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nome" class="form-label">Nome</label>
            <input type="text" class="form-control" name="nome" value="{{ $usuario->nome }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input type="email" class="form-control" name="email" value="{{ $usuario->email }}" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Nova Senha (deixe em branco para manter a atual)</label>
            <input type="password" class="form-control" name="senha">
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
</div>
@endsection
