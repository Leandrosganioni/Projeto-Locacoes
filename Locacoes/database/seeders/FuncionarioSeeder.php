<?php
/**
 * FuncionarioSeeder file.
 *
 * Seeder for creating sample Funcionario records in the database.
 *
 * PHP version 8
 *
 * @category DatabaseSeeder
 * @package  Database\Seeders
 * @author   Leandro <leandrosgani@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo/Projeto-Locacoes
 */
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funcionario;
use Faker\Factory as Faker;

/**
 * Seeder for creating sample Funcionario records.
 *
 * @category DatabaseSeeder
 * @package  Database\Seeders
 * @author   Leandro <leandrosgani@gmail.com>
 * @license  MIT https://opensource.org/licenses/MIT
 * @link     https://github.com/your-repo/Projeto-Locacoes
 */
class FuncionarioSeeder extends Seeder
{
    /**
     * Run the database seeds for Funcionario.
     *
     * @return void
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        for ($i = 0; $i < 3; $i++) {
            Funcionario::create(
                [
                    'nome' => $faker->name,
                    'cpf' => $faker->cpf(false),
                    'telefone' => $faker->phoneNumber,
                ]
            );
        }
    }
}
