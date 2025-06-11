<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Adicione esta linha para chamar o seu novo seeder
        $this->call([
            FuncionarioSeeder::class,
        ]);
    }
}