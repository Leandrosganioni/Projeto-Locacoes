<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucaoEQuebra extends Model
{
    use HasFactory;

    protected $table = 'devolucoes_e_quebras';

    protected $fillable = [
        'pedido_id',
        'material_id',
        'quantidade',
        'motivo',
        'tipo',
        'status',
        'custo_reparacao',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class, 'material_id');
    }
}