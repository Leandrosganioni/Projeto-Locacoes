<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucaoEQuebra extends Model
{
    use HasFactory;

    /**
     * Define o nome da tabela se ela for diferente do plural do model.
     * (Seu ERS indica a tabela 'devolucoes') 
     */
    protected $table = 'devolucoes';
}