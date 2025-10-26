<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            // Adiciona a coluna para rastrear itens quebrados/em manutenção
            // Colocamos depois da 'quantidade_disponivel' para organizar
            $table->integer('quantidade_quebrada')->default(0)->after('quantidade_disponivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->dropColumn('quantidade_quebrada');
        });
    }
};