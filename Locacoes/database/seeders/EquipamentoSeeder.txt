<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipamento;
use Faker\Factory as Faker;

class EquipamentoSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $tipos = [
            'Betoneira',
            'Compactador de Solo',
            'Furadeira Industrial',
            'Serra Circular',
            'Andaime Tubular',
            'Caçamba de Entulho',
            'Escavadeira Hidráulica',
            'Mini-Retroescavadeira',
            'Empilhadeira Elétrica',
            'Rompedor Hidráulico',
            'Gerador de Energia',
            'Plataforma Elevatória',
            'Lixadeira Orbital',
            'Martelo Demolidor',
            'Bomba Submersível',
            'Compactador de Percussão'
        ];

        foreach ($tipos as $tipo) {
            Equipamento::create([
                'nome' => $tipo . ' - ' . $faker->bothify('Modelo ##??'),
                'tipo' => $tipo,
                'quantidade' => $faker->numberBetween(1, 20),
                'descricao_tecnica' => $faker->paragraph,
                'informacoes_manutencao' => $faker->boolean ? $faker->sentence : null,
                'disponivel' => $faker->boolean(85),
            ]);
        }
    }
}
