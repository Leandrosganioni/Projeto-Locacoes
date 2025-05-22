<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedido_produto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipamento_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedido_produto');
    }
};

