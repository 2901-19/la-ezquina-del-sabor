<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jornadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_apertura_id')->constrained('usuarios')->onDelete('cascade');
            $table->foreignId('usuario_cierre_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->dateTime('fecha_apertura');
            $table->dateTime('fecha_cierre')->nullable();
            $table->decimal('tasa_bcv_apertura', 10, 2)->nullable();
            $table->decimal('tasa_bcv_cierre', 10, 2)->nullable();
            $table->enum('estado', ['abierta', 'cerrada'])->default('abierta');
            $table->json('resumen_cierre_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jornadas');
    }
};
