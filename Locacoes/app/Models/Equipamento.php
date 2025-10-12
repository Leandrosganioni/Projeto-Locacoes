<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipamento extends Model
{
    protected $fillable = [
        'nome',
        'tipo',
        'quantidade',
        'descricao_tecnica',
        'informacoes_manutencao',
        'imagem',
        'daily_rate',
        'quantidade_total',
        'quantidade_disponivel',
    ];

    public static function consultarEstoque()
    {
        return self::select('id', 'nome', 'quantidade_total', 'quantidade_disponivel', 'daily_rate')
            ->orderBy('nome')
            ->get();
    }

    
    public function getWeeklyRateAttribute(): float
    {
        return round((float)$this->daily_rate * 7 * 0.90, 2); 
    }

    public function getMonthlyRateAttribute(): float
    {
        return round((float)$this->daily_rate * 30 * 0.80, 2); 
    }

    
    public function reservar(int $qtd): bool
    {
        if ($qtd < 1) return false;
        if ($this->quantidade_disponivel < $qtd) return false;

        $this->quantidade_disponivel -= $qtd;
        return $this->save();
    }

    public function liberar(int $qtd): bool
    {
        if ($qtd < 1) return false;
        $this->quantidade_disponivel += $qtd;
        if ($this->quantidade_disponivel > $this->quantidade_total) {
            $this->quantidade_disponivel = $this->quantidade_total;
        }
        return $this->save();
    }

    public function retirar(int $qtd): bool
    {

        return true;
    }

    public function devolver(int $qtd): bool
    {
        
        return $this->liberar($qtd);
    }

}
