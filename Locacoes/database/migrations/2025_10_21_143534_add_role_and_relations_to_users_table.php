<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Função 'up': É executada quando aplica a migration.
     * Ela vai adicionar as colunas na tabela 'users'.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // 1. Adiciona a coluna 'role' (papel/função)
            // Usamos ->after('email') para organizar a coluna no banco, logo após o email.
            // Os papéis permitidos serão 'admin', 'funcionario' ou 'cliente'.
            $table->string('role')->after('email');

            // 2. Adiciona a coluna 'cliente_id'
            // unsignedBigInteger é o tipo padrão para chaves estrangeiras no Laravel.
            // ->nullable() permite que este campo fique vazio (um funcionário não é um cliente).
            // ->after('role') para organizar.
            $table->unsignedBigInteger('cliente_id')->nullable()->after('role');

            // 3. Adiciona a coluna 'funcionario_id'
            $table->unsignedBigInteger('funcionario_id')->nullable()->after('cliente_id');

            
            // colunas que vão armazenar o papel do usuário (role)
            // e os IDs de vínculo com as tabelas 'clientes' e 'funcionarios'.
            // Um usuário será 'admin' (sem vínculo), 
            // ou 'funcionario' (com funcionario_id), 
            // ou 'cliente' (com cliente_id).
        });
    }

    /**
     * Reverse the migrations.
     *
     * Função 'down': É executada se precisar desfazer a migration.
     * Ela remove as colunas .
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove as colunas na ordem inversa da criação
            $table->dropColumn('funcionario_id');
            $table->dropColumn('cliente_id');
            $table->dropColumn('role');
        });
    }
};