<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cliente_id',
        'funcionario_id',
        'local_entrega',
        'data_entrega'
    ];

    // Relacionamento com Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relacionamento com Funcionário
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    // Relacionamento com os itens do pedido (pedido_produto)
    public function itens()
    {
        return $this->hasMany(PedidoProduto::class);
    }
}
