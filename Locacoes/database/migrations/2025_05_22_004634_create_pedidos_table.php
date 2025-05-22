<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('funcionario_id')->constrained()->onDelete('cascade');
            $table->string('local_entrega');
            $table->date('data_entrega');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pedidos');
    }
};
