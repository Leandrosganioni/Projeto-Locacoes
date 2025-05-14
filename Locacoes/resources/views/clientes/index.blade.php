@extends('layouts.app')

@section('title', 'Clientes')

@section('content')

<p>Usuário: {{ Auth::user()->name }}</p>  

    <form method="POST" action="/logout">
        @csrf
        <button type="submit" class="btn btn-danger">Sair</button>
    </form>

<div class="container mt-5">
    <h1 class="mb-4">Lista de Clientes</h1>
    
    <a href="{{ route('clientes.create') }}" class="btn btn-primary mb-3">Novo Cliente</a>

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
            @forelse($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->cpf }}</td>
                    <td>{{ $cliente->telefone }}</td>
                    <td>
                        <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum cliente cadastrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection