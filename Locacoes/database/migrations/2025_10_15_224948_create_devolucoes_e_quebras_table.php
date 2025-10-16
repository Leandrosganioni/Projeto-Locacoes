<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devolucoes_e_quebras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos');
            $table->foreignId('material_id')->constrained('equipamentos');
            $table->integer('quantidade');
            $table->text('motivo');
            $table->string('tipo'); // 'quebra' ou 'devolucao'
            $table->string('status')->nullable(); // Ex: 'substituido', 'descartado'
            $table->decimal('custo_reparacao', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devolucoes_e_quebras');
    }
};