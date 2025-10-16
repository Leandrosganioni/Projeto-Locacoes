<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    /**
     * Relacionamento N:M (Muitos para Muitos) entre Pedido e Equipamento,
     * utilizando a tabela pivô 'pedido_produto'.
     *
     * Este relacionamento é usado para carregar os itens alugados no pedido.
     * Ele agora usa os nomes exatos das colunas da tabela pivô para
     * evitar o erro 'Unknown column'.
     */
    public function itensDoPedido()
    {
        return $this->belongsToMany(Equipamento::class, 'pedido_produto', 'pedido_id', 'equipamento_id')
                    ->withPivot('quantidade', 'start_at', 'end_at', 'computed_total', 'daily_rate_snapshot');
    }
}