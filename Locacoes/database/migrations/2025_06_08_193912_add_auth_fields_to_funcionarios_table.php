<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('telefone');
            $table->string('password')->nullable()->after('email');
            $table->string('nivel_acesso')->default('FUNCIONARIO')->after('password');
            $table->rememberToken();
        });
    }

    public function down(): void
    {
        Schema::table('funcionarios', function (Blueprint $table) {
            $table->dropColumn(['email', 'password', 'nivel_acesso', 'remember_token']);
        });
    }
};