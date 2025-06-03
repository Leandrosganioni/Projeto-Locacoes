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
            'name' => "adm", //'name' => $reques->name,
            'email' => "adm@adm.com", //'email' => $request->email,
            'password' => Hash::make("123"), 
                        //'password' => Hash::make($request->password),
        ]);
        
        $this->call([
            ClienteSeeder::class,
            FuncionarioSeeder::class,
            EquipamentoSeeder::class,
    ]);

    }
}
