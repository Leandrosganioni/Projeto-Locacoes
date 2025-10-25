@extends('layouts.app') @section('content')
<div class="container-fluid" style="background-color: #F2F2F2; min-height: 100vh;">
    <div class="row justify-content-center">
        <div class="col-md-10"> <div class="card" style="margin-top: 20px;">
                <div class="card-header">
                    <h4 class="mb-0">F_S01: Relação do Estoque</h4>
                </div>
                <div class="card-body">
                    <p>Este relatório apresenta a relação de equipamentos em estoque, suas quantidades e valores de locação.</p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="relatorioEstoqueTable">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Nome do Produto</th>
                                    <th>Valor de Venda (Diária)</th>
                                    <th>Qtd. Disponível</th>
                                    <th>Qtd. Máxima (Total)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($estoque as $item)
                                    <tr>
                                        <td>{{ $item->nome }}</td>
                                        <td>R$ {{ number_format($item->valor_venda, 2, ',', '.') }}</td>
                                        <td>{{ $item->quantidade_disponivel }}</td>
                                        <td>{{ $item->quantidade_maxima }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Nenhum equipamento encontrado.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    
    $(document).ready(function() {
        $('#relatorioEstoqueTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Portuguese-Brasil.json"
            }
        });
    });
</script>
@endpush