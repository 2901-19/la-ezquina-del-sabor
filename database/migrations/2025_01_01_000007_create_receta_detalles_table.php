<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->onDelete('cascade');
            $table->foreignId('receta_base_id')->nullable()->constrained('recetas')->onDelete('cascade');
            $table->unsignedBigInteger('materia_prima_id')->nullable();
            $table->decimal('cantidad_requerida', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receta_detalles');
    }
};
