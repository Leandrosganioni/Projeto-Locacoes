<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'funcionario_id', 'local_entrega', 'data_entrega' 
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
    

    public function itens()
    {
        return $this->hasMany(PedidoProduto::class, 'pedido_id');
    }

    public function equipamentos()
    {
        return $this->belongsToMany(Equipamento::class, 'pedido_produto', 'pedido_id', 'equipamento_id')
                    ->withPivot('quantidade', 'start_at', 'end_at', 'computed_total', 'daily_rate_snapshot', 'status');
    }

    public function devolucoesEQuebras()
    {
        return $this->hasMany(DevolucaoEQuebra::class, 'pedido_id');
    }
}