<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoProduto extends Pivot 
{
    use HasFactory;

    protected $fillable = [
        'pedido_id', 
        'equipamento_id', 
        'quantidade', 
        'status', 
        'daily_rate_snapshot'
    ];

    const STATUS_RESERVADO = 'reservado';

    const STATUS_DEVOLVIDO = 'devolvido';
    const STATUS_EM_LOCACAO = 'em_locacao';
    const STATUS_CANCELADO = 'cancelado';
    
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class, 'equipamento_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function reservar(): bool
    {
        $equip = $this->equipamento; // Carrega o equipamento
        if (!$equip) return false;
        
        return $equip->reservar($this->quantidade);
    }
    
    public function cancelar(): bool
    {
        $equip = $this->equipamento; // Carrega o equipamento
        if (!$equip) return false;

        if ($this->status == self::STATUS_RESERVADO) { 
             return $equip->liberar($this->quantidade);
        }
        return true; 
    }
}