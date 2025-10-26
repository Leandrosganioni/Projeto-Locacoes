@extends('layouts.app')

@section('content')
<div class="container-fluid" style="background-color: #F2F2F2; height: 100vh; padding-top: 20px;">
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-12">
                <h2><i class="fas fa-exclamation-triangle"></i> Registrar Ocorrência (Quebra/Devolução)</h2>
                <hr>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Ops!</strong> Havia problemas com os dados informados:<br><br>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('ocorrencias.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="equipamento_nome">Equipamento:</label>
                                <input type="text" class="form-control" id="equipamento_nome" value="{{ $equipamento->nome }}" readonly>
                                <input type="hidden" name="equipamento_id" value="{{ $equipamento->id }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="estoque_atual">Estoque (Disponível / Total):</label>
                                <input type="text" class="form-control" id="estoque_atual" value="{{ $equipamento->quantidade_disponivel }} / {{ $equipamento->quantidade_total }}" readonly>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tipo"><strong>* Tipo de Ocorrência:</strong></label>
                                <select class="form-control" id="tipo" name="tipo" required>
                                    <option value="">Selecione...</option>
                                    <option value="quebra" {{ old('tipo') == 'quebra' ? 'selected' : '' }}>Quebra (Dano interno / Estoque)</option>
                                    <option value="devolucao" {{ (old('tipo') == 'devolucao' || $pedido) ? 'selected' : '' }}>Devolução (Retorno de Cliente)</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="motivo"><strong>* Motivo:</strong></label>
                                <select class="form-control" id="motivo" name="motivo" required>
                                    <option value="">Selecione...</option>
                                    <option value="avaria" {{ old('motivo') == 'avaria' ? 'selected' : '' }}>Avaria (Ex: Risco, quebra parcial)</option>
                                    <option value="defeito" {{ old('motivo') == 'defeito' ? 'selected' : '' }}>Defeito (Ex: Não liga, funcional)</option>
                                    <option value="validade_expirada" {{ old('motivo') == 'validade_expirada' ? 'selected' : '' }}>Validade Expirada</option>
                                    <option value="outro" {{ old('motivo') == 'outro' ? 'selected' : '' }}>Outro</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantidade"><strong>* Quantidade Afetada:</strong></label>
                                <input type="number" class="form-control" id="quantidade" name="quantidade" 
                                       value="{{ old('quantidade', 1) }}" 
                                       min="1" 
                                       max="{{ $equipamento->quantidade_disponivel }}" required>
                                <small>Máximo disponível: {{ $equipamento->quantidade_disponivel }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3" id="campo_motivo_outro" style="display: {{ old('motivo') == 'outro' ? 'block' : 'none' }};">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="motivo_outro">* Especifique o motivo ("Outro"):</label>
                                <input type="text" class="form-control" id="motivo_outro" name="motivo_outro" value="{{ old('motivo_outro') }}">
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">Vincular Ocorrência (Opcional)</h5>
                    <small>Selecione se for uma devolução de cliente ou quebra específica de um pedido.</small>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cliente_id">Cliente:</label>
                                <select class="form-control" id="cliente_id" name="cliente_id" data-api-url="{{ url('/api/clientes') }}">
                                    <option value="">Selecione um cliente (se aplicável)...</option>
                                    @foreach($clientes as $cli)
                                        <option value="{{ $cli->id }}" {{ (old('cliente_id') == $cli->id || (isset($cliente) && $cliente->id == $cli->id)) ? 'selected' : '' }}>
                                            {{ $cli->nome }} ({{ $cli->cpf_cnpj }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pedido_id">Pedido (Nº):</label>
                                <select class="form-control" id="pedido_id" name="pedido_id" {{ !$cliente ? 'disabled' : '' }}>
                                    @if($cliente && $pedidosDoCliente->count() > 0)
                                        <option value="">Selecione um pedido (se aplicável)...</option>
                                        @foreach($pedidosDoCliente as $ped)
                                            <option value="{{ $ped->id }}" {{ (old('pedido_id') == $ped->id || (isset($pedido) && $pedido->id == $ped->id)) ? 'selected' : '' }}>
                                                {{-- Corrigido para usar data_entrega --}}
                                                Pedido #{{ $ped->id }} ({{ \Carbon\Carbon::parse($ped->data_entrega)->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    @elseif($cliente)
                                        <option value="">Nenhum pedido encontrado para este cliente</option>
                                    @else
                                        <option value="">Selecione um cliente primeiro...</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="observacao">Observações Adicionais:</label>
                                <textarea class="form-control" id="observacao" name="observacao" rows="3">{{ old('observacao') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        {{-- Botão Cancelar (cinza claro) --}}
                        <a href="{{ route('equipamentos.index') }}" class="btn btn-outline-secondary">
                             <i class="bi bi-x-circle me-1"></i> Cancelar
                        </a>
                        {{-- Botão Salvar (azul) --}}
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Registrar Ocorrência
                        </button>
                        {{-- Mudei o texto para ficar mais curto como nos outros forms --}}
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/ocorrencias_create.js') }}"></script>
@endpush