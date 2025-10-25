@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Bloco para exibir erros de validação --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Bloco para exibir mensagens de erro do controlador --}}
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <h1>Registrar Quebra de Equipamento</h1>
    <p>Pedido #{{ $pedido->id }}</p>

    <form action="{{ route('quebras.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

        <div class="form-group">
            <label for="material_id">Equipamento Quebrado:</label>
            {{-- CORREÇÃO: O nome do campo agora é 'material_id' --}}
            <select name="material_id" id="material_id" class="form-control" required>
                @foreach ($pedido->itens as $item)
                    <option value="{{ $item->equipamento->id }}" data-quantidade-locada="{{ $item->quantidade }}">
                        {{ $item->equipamento->nome }} ({{ $item->quantidade }} alugados)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="quantidade">Quantidade quebrada:</label>
            <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required placeholder="Máx: 1">
        </div>

        <div class="form-group mt-3">
            <label for="motivo">Motivo da quebra:</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-danger mt-4">Registrar Quebra</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectEquipamento = document.getElementById('material_id'); // CORREÇÃO
        const inputQuantidade = document.getElementById('quantidade');

        function atualizarMaximo() {
            try {
                const selectedOption = selectEquipamento.options[selectEquipamento.selectedIndex];
                const max = selectedOption.dataset.quantidadeLocada;
                
                if (max) {
                    inputQuantidade.max = max;
                    inputQuantidade.placeholder = `Máx: ${max}`;
                } else {
                    inputQuantidade.removeAttribute('max');
                    inputQuantidade.placeholder = '';
                }
            } catch (e) {
                inputQuantidade.removeAttribute('max');
                inputQuantidade.placeholder = '';
            }
        }

        selectEquipamento.addEventListener('change', atualizarMaximo);
        atualizarMaximo();
    });
</script>
@endpush