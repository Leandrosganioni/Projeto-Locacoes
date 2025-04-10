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
        'disponivel'
    ];

    //(F_B04)
    public static function consultarEstoque()
    {
        return self::select('nome', 'quantidade', 'disponivel')
                 ->orderBy('nome')
                 ->get();
    }
}
