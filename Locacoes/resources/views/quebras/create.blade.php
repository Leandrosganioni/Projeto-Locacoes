@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Quebra de Equipamento</h1>
    <p>Pedido #{{ $pedido->id }}</p>

    <form action="{{ route('quebras.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

        <div class="form-group">
            <label for="equipamento_id">Equipamento Quebrado:</label>
            <select name="equipamento_id" id="equipamento_id" class="form-control" required>
                {{-- --- CORREÇÃO AQUI --- --}}
                @foreach ($pedido->itens as $item)
                    {{-- Adicionamos o 'data-quantidade-locada' para o JavaScript ler --}}
                    <option value="{{ $item->equipamento->id }}" data-quantidade-locada="{{ $item->quantidade }}">
                        {{ $item->equipamento->nome }} ({{ $item->quantidade }} alugados)
                    </option>
                @endforeach
                {{-- --- FIM DA CORREÇÃO --- --}}
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="quantidade">Quantidade quebrada:</label>
            {{-- Adicionamos um 'placeholder' --}}
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

{{-- --- SCRIPT ADICIONADO --- --}}
@push('scripts')
<script>
    // Espera o documento carregar
    document.addEventListener('DOMContentLoaded', function() {
        const selectEquipamento = document.getElementById('equipamento_id');
        const inputQuantidade = document.getElementById('quantidade');

        // Função para atualizar o 'max' do input de quantidade
        function atualizarMaximo() {
            try {
                // Pega o <option> que está selecionado
                const selectedOption = selectEquipamento.options[selectEquipamento.selectedIndex];
                
                // Pega o valor do nosso data-attribute
                const max = selectedOption.dataset.quantidadeLocada;
                
                // Define o 'max' e o 'placeholder' do input
                if (max) {
                    inputQuantidade.max = max;
                    inputQuantidade.placeholder = `Máx: ${max}`;
                } else {
                    inputQuantidade.removeAttribute('max');
                    inputQuantidade.placeholder = '';
                }
            } catch (e) {
                // Se não houver itens, apenas ignora
                inputQuantidade.removeAttribute('max');
                inputQuantidade.placeholder = '';
            }
        }

        // Adiciona um "ouvinte" que dispara a função sempre que o usuário
        // trocar o equipamento no <select>
        selectEquipamento.addEventListener('change', atualizarMaximo);
        
        // Roda a função uma vez assim que a página carrega
        // para já definir o 'max' do primeiro item da lista
        atualizarMaximo();
    });
</script>
@endpush