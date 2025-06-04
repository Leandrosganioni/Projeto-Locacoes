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

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }

    public function produtos()
    {
        return $this->belongsToMany(Equipamento::class, 'pedido_produto')
            ->withPivot('quantidade')
            ->withTimestamps();
    }
}
