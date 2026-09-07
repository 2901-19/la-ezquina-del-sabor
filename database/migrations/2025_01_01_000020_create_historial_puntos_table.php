<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('comanda_id')->nullable()->constrained('comandas')->onDelete('set null');
            $table->integer('puntos_variacion');
            $table->enum('tipo_operacion', ['acumulacion', 'canje']);
            $table->dateTime('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_puntos');
    }
};
