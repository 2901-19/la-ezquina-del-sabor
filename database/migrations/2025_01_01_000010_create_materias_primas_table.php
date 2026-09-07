<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias_primas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('unidad_medida');
            $table->decimal('stock_actual', 10, 2)->default(0);
            $table->decimal('stock_minimo', 10, 2)->default(0);
            $table->decimal('costo_unitario_usd', 10, 2)->default(0);
            $table->date('ultima_actualizacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias_primas');
    }
};
