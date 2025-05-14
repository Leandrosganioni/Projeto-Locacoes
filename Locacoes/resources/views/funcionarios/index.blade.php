@extends('layouts.app')

@section('title', 'Funcionários')

@section('content')

<p>Usuário: {{ Auth::user()->name }}</p>  

    <form method="POST" action="/logout">
        @csrf
        <button type="submit" class="btn btn-danger">Sair</button>
    </form>

<div class="container mt-5">
    <h1 class="mb-4">Lista de Funcionários</h1>
    
    <a href="{{ route('funcionarios.create') }}" class="btn btn-primary mb-3">Novo Funcionário</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($funcionarios as $funcionario)
                <tr>
                    <td>{{ $funcionario->nome }}</td>
                    <td>{{ $funcionario->cpf }}</td>
                    <td>{{ $funcionario->telefone }}</td>
                    <td>
                        <a href="{{ route('funcionarios.show', $funcionario->id) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('funcionarios.edit', $funcionario->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('funcionarios.destroy', $funcionario->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir este funcionário?')">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum funcionário cadastrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection