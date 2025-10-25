<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * (Os campos que podem ser preenchidos em massa)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // <-- Adicionado
        'cliente_id', // <-- Adicionado
        'funcionario_id', // <-- Adicionado
    ];

    /**
     * The attributes that should be hidden for serialization.
     * (Campos que não devem aparecer em respostas JSON, como a senha)
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     * (Define tipos de dados)
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // Garante que a senha seja criptografada
        ];
    }

    // --- Documentação (Novos Relacionamentos) ---

    /**
     * Define o relacionamento: Um Usuário (User) PERTENCE A (belongsTo) um Cliente.
     * Isso nos permitirá fazer: Auth::user()->cliente->nome
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Define o relacionamento: Um Usuário (User) PERTENCE A (belongsTo) um Funcionário.
     * Isso nos permitirá fazer: Auth::user()->funcionario->cargo
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class);
    }
}