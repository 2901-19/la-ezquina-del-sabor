<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receta_detalles', function (Blueprint $table) {
            $table->foreign('materia_prima_id')
                ->references('id')
                ->on('materias_primas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('receta_detalles', function (Blueprint $table) {
            $table->dropForeign(['materia_prima_id']);
        });
    }
};
