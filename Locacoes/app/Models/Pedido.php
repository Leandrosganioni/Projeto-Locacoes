<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id', 'funcionario_id', 'local_entrega', 'data_entrega' 
        // Adicione outros campos se necessário, mas os acima parecem
        // ser usados no seu PedidoController::store
    ];

    // --- RELACIONAMENTOS ESSENCIAIS ---

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
    
    /**
     * ESTA É A MUDANÇA PRINCIPAL!
     * O Pedido "tem muitos" itens (registros da tabela pivô PedidoProduto).
     * É ISSO que a sua view (show.blade.php) espera.
     */
    public function itens()
    {
        // Isso retorna uma coleção de 'PedidoProduto'
        return $this->hasMany(PedidoProduto::class, 'pedido_id');
    }
    
    /**
     * Este é o relacionamento que tínhamos antes (belongsToMany).
     * Mudei o nome para 'equipamentos' para não dar conflito.
     * Ele retorna diretamente os equipamentos.
     */
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