<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // Mude de Model para Pivot se for tabela pivô
use Illuminate\Database\Eloquent\Factories\HasFactory;

// É comum que o model da tabela pivô estenda Pivot
class PedidoProduto extends Pivot 
{
    use HasFactory;

    // Se não estender Pivot, descomente a linha abaixo
    // protected $table = 'pedido_produto'; 

    // Adicione os campos do 'store'
    protected $fillable = [
        'pedido_id', 
        'equipamento_id', 
        'quantidade', 
        'status', 
        'daily_rate_snapshot'
    ];

    // --- CORREÇÃO AQUI ---
    // Definição dos status (pelo seu controller)
    const STATUS_RESERVADO = 'reservado';

    // Status que a sua view (show.blade.php) também precisa
    const STATUS_DEVOLVIDO = 'devolvido';
    const STATUS_EM_LOCACAO = 'em_locacao';
    const STATUS_CANCELADO = 'cancelado';
    // --- FIM DA CORREÇÃO ---


    /**
     * ESTE É O RELACIONAMENTO QUE O ERRO PROCURA!
     * Um 'Item do Pedido' (PedidoProduto) "pertence a um" Equipamento.
     */
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class, 'equipamento_id');
    }

    /**
     * Relacionamento reverso (opcional, mas bom ter)
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // --- Funções do seu Controller ---
    // Movi a lógica do seu controller para o model
    
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

        // Só libera se estava reservado
        if ($this->status == self::STATUS_RESERVADO) { 
             return $equip->liberar($this->quantidade);
        }
        // Se já estava em locação, a lógica pode ser outra (ex: devolver)
        return true; 
    }
}