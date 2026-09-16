<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {
            $table->decimal('monto_inicial', 10, 2)->nullable()->after('resumen_cierre_json');
            $table->decimal('monto_final', 10, 2)->nullable()->after('monto_inicial');
        });
    }

    public function down(): void
    {
        Schema::table('jornadas', function (Blueprint $table) {
            $table->dropColumn(['monto_inicial', 'monto_final']);
        });
    }
};
