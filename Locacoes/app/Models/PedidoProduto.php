<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoProduto extends Model
{
    
    protected $table = 'pedido_produto'; 

    
    protected $fillable = [
        'pedido_id',
        'equipamento_id',
        'quantidade'
    ];

    
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }
}
