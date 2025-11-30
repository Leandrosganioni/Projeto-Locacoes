<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            // Adiciona a coluna status se ela não existir
            if (!Schema::hasColumn('pedidos', 'status')) {
                $table->string('status')->default('ativo')->after('data_entrega');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};