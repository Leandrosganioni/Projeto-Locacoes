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
    public function getCpfFormatadoAttribute()
    {
        $valor = preg_replace('/[^0-9]/', '', $this->cpf);

        if (strlen($valor) === 11) {
            return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9, 2);
        }

        return $this->cpf;
    }

    public function getTelefoneFormatadoAttribute()
    {
        $valor = preg_replace('/[^0-9]/', '', $this->telefone);

        if (strlen($valor) === 11) {
            return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 5) . '-' . substr($valor, 7, 4);
        } elseif (strlen($valor) === 10) {
            return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 4) . '-' . substr($valor, 6, 4);
        }

        return $this->telefone;
    }
}