@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Registrar Quebra de Equipamento</h1>
    <p>Pedido #{{ $pedido->id }}</p>

    <form action="{{ route('quebras.store') }}" method="POST">
        @csrf
        <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

        <div class="form-group">
            <label for="material_id">Equipamento Quebrado:</label>
            <select name="material_id" id="material_id" class="form-control" required>
                @foreach ($pedido->itensDoPedido as $item)
                    <option value="{{ $item->material->id }}">
                        {{ $item->material->nome }} ({{ $item->quantidade }} alugados)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-3">
            <label for="quantidade">Quantidade quebrada:</label>
            <input type="number" name="quantidade" id="quantidade" class="form-control" min="1" required>
        </div>

        <div class="form-group mt-3">
            <label for="motivo">Motivo da quebra:</label>
            <textarea name="motivo" id="motivo" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-danger mt-4">Registrar Quebra</button>
    </form>
</div>
@endsection