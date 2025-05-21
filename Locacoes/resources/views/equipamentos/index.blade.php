@extends('layouts.app')

@section('title', 'Equipamentos')

@section('content')


    <form method="POST" action="/logout">
        @csrf
    </form>

<div class="container mt-5">
    <h1 class="mb-4">Lista de Equipamentos</h1>
    
    <a href="{{ route('equipamentos.create') }}" class="btn btn-primary mb-3">Novo Equipamento</a>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($equipamentos as $equipamento)
                <tr>
                    <td>{{ $equipamento->nome }}</td>
                    <td>{{ $equipamento->tipo }}</td>
                    <td>{{ $equipamento->quantidade }}</td>
                    <td>
                        <a href="{{ route('equipamentos.show', $equipamento->id) }}" class="btn btn-sm btn-info">Ver</a>
                        <a href="{{ route('equipamentos.edit', $equipamento->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('equipamentos.destroy', $equipamento->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Tem certeza que deseja excluir este equipamento?')">
                                Excluir
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Nenhum equipamento cadastrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection