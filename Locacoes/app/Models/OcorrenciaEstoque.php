<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OcorrenciaEstoque extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'ocorrencias_estoque';

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'equipamento_id',
        'user_id',
        'pedido_id',
        'cliente_id',
        'tipo',
        'motivo',
        'motivo_outro',
        'quantidade',
        'observacao',
    ];

    /**
     * Retorna o usuário (admin/vendas) que registrou a ocorrência.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retorna o equipamento afetado pela ocorrência.
     */
    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    /**
     * Retorna o pedido associado (se for uma devolução).
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    /**
     * Retorna o cliente associado (se for uma devolução).
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}