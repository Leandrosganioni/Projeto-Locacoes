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

    // Preços derivados (semana/mês) – regras podem ser ajustadas depois
    public function getWeeklyRateAttribute(): float
    {
        return round((float)$this->daily_rate * 7 * 0.90, 2); // 10% off
    }

    public function getMonthlyRateAttribute(): float
    {
        return round((float)$this->daily_rate * 30 * 0.80, 2); // 20% off
    }

    // Estoque: reservar / liberar / retirar / devolver
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
        // Se já reservou antes, normalmente nada muda aqui.
        // Mantemos para futura evolução (ex.: separação física).
        return true;
    }

    public function devolver(int $qtd): bool
    {
        // Na devolução, volta para o disponível
        return $this->liberar($qtd);
    }

}
