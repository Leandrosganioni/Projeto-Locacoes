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
        Schema::create('ocorrencias_estoque', function (Blueprint $table) {
            $table->id();

            // --- Chaves Estrangeiras ---
            // Equipamento que sofreu a ocorrência
            $table->foreignId('equipamento_id')->constrained('equipamentos')->onDelete('cascade');

            // Usuário que registrou (admin/vendas)
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');

            // Vínculos opcionais (pode ser uma quebra interna ou uma devolução de pedido)
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->onDelete('set null');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');

            // --- Dados da Ocorrência ---
            $table->enum('tipo', ['quebra', 'devolucao'])->comment('Quebra (dano no estoque) ou Devolução (retorno de cliente)');
            $table->enum('motivo', ['avaria', 'defeito', 'validade_expirada', 'outro']);
            $table->string('motivo_outro')->nullable()->comment('Descrição se o motivo for "outro"');
            $table->integer('quantidade');
            $table->text('observacao')->nullable();

            $table->timestamps(); // created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias_estoque');
    }
};