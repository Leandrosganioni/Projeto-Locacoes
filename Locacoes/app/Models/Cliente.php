<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    
    
    protected $fillable = ['nome', 'cpf_cnpj', 'telefone', 'endereco', 'email'];

    //um cliente tem muitos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    
    /**
     * Define o relacionamento inverso: Um Cliente TEM UM (hasOne) Usuário (User).
     *$cliente->user->email
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}