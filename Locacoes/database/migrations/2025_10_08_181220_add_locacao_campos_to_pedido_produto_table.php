<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pedido_produto', function (Blueprint $table) {
            $table->dateTime('start_at')->nullable()->after('quantidade');
            $table->dateTime('end_at')->nullable()->after('start_at');
            $table->string('status', 20)->default('reservado')->after('end_at');
            $table->decimal('daily_rate_snapshot', 10, 2)->nullable()->after('status');
            $table->decimal('computed_total', 10, 2)->default(0)->after('daily_rate_snapshot');
            $table->json('calc_breakdown_json')->nullable()->after('computed_total');
        });
    }

    public function down(): void
    {
        Schema::table('pedido_produto', function (Blueprint $table) {
            $table->dropColumn([
                'start_at','end_at','status',
                'daily_rate_snapshot','computed_total','calc_breakdown_json'
            ]);
        });
    }
};
?>
