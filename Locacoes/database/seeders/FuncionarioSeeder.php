<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcionario; // Importe o seu Model
use Illuminate\Support\Facades\Hash; // Importe o Hash

class FuncionarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria um funcionário para verificar se já existe um com este email
        Funcionario::firstOrCreate(
            ['email' => 'adm@adm.com'], // Chave única para evitar duplicatas
            [
                'nome' => 'Administrador Principal',
                'cpf' => '000.000.000-00',
                'telefone' => '(00) 00000-0000',
                'password' => Hash::make('123'),
                'nivel_acesso' => 'ADMINISTRADOR'
            ]
        );
    }
}