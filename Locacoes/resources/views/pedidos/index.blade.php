@extends('layouts.app')

@section('title', 'Lista de Pedidos')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Lista de Pedidos</h1>

    <a href="{{ route('pedidos.create') }}" class="btn btn-primary mb-3">Novo Pedido</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Funcionário</th>
                <th>Data de Entrega</th>
                <th>Local de Entrega</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->cliente->nome }}</td>
                    <td>{{ $pedido->funcionario->nome }}</td>
                    <td>{{ \Carbon\Carbon::parse($pedido->data_entrega)->format('d/m/Y') }}</td>
                    <td>{{ $pedido->local_entrega }}</td>
                    <td>
                        <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('pedidos.destroy', $pedido->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este pedido?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Nenhum pedido cadastrado</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
