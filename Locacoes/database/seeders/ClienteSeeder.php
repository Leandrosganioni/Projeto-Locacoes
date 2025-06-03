<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Faker\Factory as Faker;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 20; $i++) {
            Cliente::create([
                'nome' => $faker->name,
                'cpf' => $faker->cpf(false),
                'telefone' => $faker->phoneNumber,
                'endereco' => $faker->address,
            ]);
        }
    }
}
