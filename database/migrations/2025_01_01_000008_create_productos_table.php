<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->foreignId('receta_id')->nullable()->constrained('recetas')->onDelete('set null');
            $table->string('nombre');
            $table->enum('tipo_precio', ['margen', 'definido'])->default('margen');
            $table->decimal('margen_ganancia', 5, 2)->nullable();
            $table->decimal('precio_usd', 10, 2)->default(0);
            $table->boolean('es_combo')->default(false);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
