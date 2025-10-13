<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('equipamentos', function (Blueprint $table) {
            $table->decimal('daily_rate', 10, 2)->default(0)->after('tipo');
            $table->integer('quantidade_total')->default(0)->after('quantidade');
            $table->integer('quantidade_disponivel')->default(0)->after('quantidade_total');
        });

        
        DB::statement('UPDATE equipamentos SET quantidade_total = quantidade, quantidade_disponivel = quantidade');
    }

    public function down(): void
    {
        Schema::table(
            'equipamentos',
            function (Blueprint $table) {
                $table->dropColumn(['daily_rate', 'quantidade_total', 'quantidade_disponivel']);
            }
        );
    }
};
