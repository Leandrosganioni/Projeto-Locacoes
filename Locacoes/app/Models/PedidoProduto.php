<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{
    // Tabela personalizada (evita erro de nome automático no plural)
    protected $table = 'pedido_produto'; 

    // Campos permitidos para preenchimento em massa
    protected $fillable = [
        'pedido_id',
        'equipamento_id',
        'quantidade'
    ];

    // Relacionamento com Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    // Relacionamento com Equipamento
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
