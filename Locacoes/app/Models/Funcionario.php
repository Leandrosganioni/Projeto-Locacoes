<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'cpf', 'telefone', 'endereco', 'cargo', 'salario', 'data_contratacao'];

    
    /**
     * Define o relacionamento inverso: Um Funcionário TEM UM (hasOne) Usuário (User).
     * $funcionario->user->email
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}