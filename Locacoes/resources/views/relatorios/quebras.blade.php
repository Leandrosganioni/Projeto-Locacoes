@extends('layouts.app')

{{-- Alterado título e adicionado ícone de Bootstrap --}}
@section('title', 'Relatório de Quebras e Devoluções')

@section('content')
<div class="container py-5">

    {{-- Estrutura principal do card, igual ao vendas.blade.php --}}
    <div class="bg-white shadow rounded p-4 p-md-5">

        {{-- Cabeçalho com título e botão de voltar --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h2 class="mb-0 fw-semibold">Relatório de Quebras e Devoluções</h2>
            <a href="{{ route('relatorios.index') }}" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
                <i class="bi bi-arrow-left"></i> Voltar para Central
            </a>
        </div>

        {{-- Seção de Filtros (similar ao vendas.blade.php) --}}
        {{-- TODO: Implementar lógica de filtro no RelatorioController --}}
        <section class="mb-4 p-4 border rounded bg-light">
            <h5 class="fw-semibold mb-3">Filtros do Relatório</h5>
            <form method="GET" action="{{ route('relatorios.quebras') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="data_inicio" class="form-label small">Data Início</label>
                        {{-- Usar 'request()->input()' para manter os valores dos filtros --}}
                        <input type="date" class="form-control form-control-sm" id="data_inicio" name="data_inicio" value="{{ request()->input('data_inicio', \Carbon\Carbon::now()->subDays(30)->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="data_fim" class="form-label small">Data Fim</label>
                        <input type="date" class="form-control form-control-sm" id="data_fim" name="data_fim" value="{{ request()->input('data_fim', \Carbon\Carbon::now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="equipamento_filtro" class="form-label small">Equipamento (Opcional)</label>
                        {{-- TODO: Adicionar um select populado com equipamentos --}}
                        <input type="text" class="form-control form-control-sm" id="equipamento_filtro" name="equipamento_filtro" placeholder="Nome do equipamento..." value="{{ request()->input('equipamento_filtro') }}">
                    </div>
                    <div class="col-md-3 d-flex">
                        <button type="submit" class="btn btn-primary btn-sm w-100 me-2">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('relatorios.quebras') }}" class="btn btn-light btn-sm" title="Limpar Filtros">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
            </form>
        </section>

        <hr class="my-5">

        {{-- Seção de Gráficos (similar ao vendas.blade.php) --}}
        {{-- TODO: Implementar lógica de gráfico no RelatorioController e JS --}}
        <section class="mb-5">
            <h3 class="fw-semibold mb-4">Dashboard Visual</h3>
            <div class="row g-4">
                <div class="col-md-6">
                    <h5 class="text-center mb-3">Ocorrências por Motivo (Período)</h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        {{-- Canvas para o gráfico de motivos --}}
                        <canvas id="motivosChart"
                            data-labels='@json($chartMotivosLabels ?? [])' {{-- Passar dados do controller --}}
                            data-data='@json($chartMotivosData ?? [])'> {{-- Passar dados do controller --}}
                        </canvas>
                    </div>
                </div>
                <div class="col-md-6">
                    <h5 class="text-center mb-3">Ocorrências ao Longo do Tempo</h5>
                    <div style="position: relative; height: 350px; width: 100%;">
                        {{-- Canvas para o gráfico de tempo --}}
                        <canvas id="tempoChart"
                            data-labels='@json($chartTempoLabels ?? [])' {{-- Passar dados do controller --}}
                            data-data='@json($chartTempoData ?? [])'> {{-- Passar dados do controller --}}
                        </canvas>
                    </div>
                </div>
            </div>
        </section>

        <hr class="my-5">

        {{-- Seção da Tabela Detalhada --}}
        <section>
            <h3 class="fw-semibold mb-3">Dados Detalhados das Ocorrências</h3>
            <p class="text-muted mb-4">
                Histórico completo de quebras e devoluções no período selecionado.
            </p>
            <div class="table-responsive">
                {{-- Adicionado ID 'quebrasTable' para DataTables --}}
                <table class="table table-bordered table-striped table-hover align-middle" id="quebrasTable" data-pagination-enabled="{{ $ocorrencias instanceof \Illuminate\Pagination\LengthAwarePaginator ? 'true' : 'false' }}">
                    <thead class="table-dark">
                        <tr>
                            {{-- Colunas mantidas, ajustadas para DataTables --}}
                            <th>Data/Hora</th>
                            <th>Equipamento</th>
                            <th class="text-center">Qtd.</th>
                            <th>Tipo</th>
                            <th>Motivo</th>
                            <th>Obs.</th>
                            <th>Registrado por</th>
                            <th>Vínculo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ocorrencias as $ocorrencia)
                        <tr>
                            <td data-sort="{{ $ocorrencia->created_at->timestamp }}">{{ $ocorrencia->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $ocorrencia->equipamento->nome ?? 'Excluído' }}</td>
                            <td class="text-center">{{ $ocorrencia->quantidade }}</td>
                            <td>{{ ucfirst($ocorrencia->tipo) }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $ocorrencia->motivo)) }} {{-- Mostra 'Validade Expirada' etc --}}
                                @if($ocorrencia->motivo == 'outro')
                                <em>({{ $ocorrencia->motivo_outro }})</em>
                                @endif
                            </td>
                            <td title="{{ $ocorrencia->observacao ?? '' }}"> {{-- Adiciona o texto completo no title para ver ao passar o mouse --}} {{ Str::limit($ocorrencia->observacao ?? '-', 50) }}
                            </td>
                            <td>{{ $ocorrencia->user->name ?? 'Excluído' }}</td>
                            <td>
                                @if($ocorrencia->pedido_id)
                                <a href="{{ route('pedidos.show', $ocorrencia->pedido_id) }}" title="Ver Pedido">
                                    Pedido #{{ $ocorrencia->pedido_id }}
                                </a>
                                @elseif($ocorrencia->cliente_id)
                                {{ $ocorrencia->cliente->nome ?? 'Excluído' }}
                                @else
                                <span class="text-muted">Estoque</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Nenhuma ocorrência encontrada no período selecionado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Paginação (se estiver usando ->paginate() no controller) --}}
            @if ($ocorrencias instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="d-flex justify-content-center mt-4">
                {{ $ocorrencias->appends(request()->query())->links() }} {{-- Mantém filtros na paginação --}}
            </div>
            @endif
        </section>

    </div> {{-- Fim do card principal --}}
</div> {{-- Fim do container --}}
@endsection

@push('styles')
{{-- Estilos para DataTables e Ícones (igual ao vendas.blade.php) --}}
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
{{-- Scripts para JQuery, Chart.js e DataTables (igual ao vendas.blade.php) --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

{{-- Script de inicialização EXTERNO --}}
<script src="{{ asset('js/relatorio_quebras.js') }}"></script>
@endpush