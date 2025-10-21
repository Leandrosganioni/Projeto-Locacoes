<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create([
            'name' => "adm",
            'email' => "adm@adm.com",
            'password' => Hash::make("123"), 
            

            'role' => 'admin', 
            

            'cliente_id' => null,
            'funcionario_id' => null,
        ]);
        

        $this->call([
            ClienteSeeder::class,
            FuncionarioSeeder::class,

        ]);
    }
}