@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Relatório de Quebras e Devoluções</h1>
    <p class="text-muted">Histórico de todas as ocorrências registradas no sistema.</p>

    <table class="table table-striped table-hover mt-4">
        <thead class="table-dark">
            <tr>
                <th>ID Ocorrência</th>
                <th>Pedido #</th>
                <th>Equipamento</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Motivo</th>
                <th>Data Registro</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($ocorrencias as $ocorrencia)
                <tr>
                    <td>{{ $ocorrencia->id }}</td>
                    <td><a href="{{ route('pedidos.show', $ocorrencia->pedido_id) }}">#{{ $ocorrencia->pedido_id }}</a></td>
                    <td>
                        {{ $ocorrencia->equipamento->nome ?? 'Equipamento não encontrado' }} 
                        <span class="badge bg-secondary">{{ $ocorrencia->equipamento->tipo ?? '' }}</span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $ocorrencia->tipo == 'quebra' ? 'danger' : 'warning' }}">
                            {{ ucfirst($ocorrencia->tipo) }}
                        </span>
                    </td>
                    <td>{{ $ocorrencia->quantidade }}</td>
                    <td>{{ $ocorrencia->motivo }}</td>
                    <td>{{ $ocorrencia->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Nenhuma ocorrência de quebra ou devolução registrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection