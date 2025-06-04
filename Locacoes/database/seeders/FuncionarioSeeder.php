<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcionario;
use Faker\Factory as Faker;

class FuncionarioSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 10; $i++) {
            Funcionario::create([
                'nome' => $faker->name,
                'cpf' => $faker->cpf(false),
                'telefone' => $faker->phoneNumber,
            ]);
        }
    }
}
