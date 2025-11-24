<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    

    protected $fillable = ['nome', 'cpf', 'telefone', 'endereco'];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Define o relacionamento inverso: Um Cliente TEM UM (hasOne) Usuário (User).
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
    public function getCpfCnpjFormatadoAttribute()
    {
        // Limpa tudo que não é número da coluna 'cpf' do banco
        $valor = preg_replace('/[^0-9]/', '', $this->cpf); 

        if (strlen($valor) === 11) {
            // Formata CPF: 000.000.000-00
            return substr($valor, 0, 3) . '.' . substr($valor, 3, 3) . '.' . substr($valor, 6, 3) . '-' . substr($valor, 9, 2);
        } elseif (strlen($valor) === 14) {
            // Formata CNPJ: 00.000.000/0000-00
            return substr($valor, 0, 2) . '.' . substr($valor, 2, 3) . '.' . substr($valor, 5, 3) . '/' . substr($valor, 8, 4) . '-' . substr($valor, 12, 2);
        }

        return $this->cpf; // Retorna original se não tiver o tamanho certo
    }

    // Formatador para Telefone
    public function getTelefoneFormatadoAttribute()
    {
        $valor = preg_replace('/[^0-9]/', '', $this->telefone);

        if (strlen($valor) === 11) {
            // Celular: (00) 00000-0000
            return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 5) . '-' . substr($valor, 7, 4);
        } elseif (strlen($valor) === 10) {
            // Fixo: (00) 0000-0000
            return '(' . substr($valor, 0, 2) . ') ' . substr($valor, 2, 4) . '-' . substr($valor, 6, 4);
        }

        return $this->telefone;
    }
}