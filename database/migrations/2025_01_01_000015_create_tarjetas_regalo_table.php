<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarjetas_regalo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->string('codigo')->unique();
            $table->decimal('valor_usd', 10, 2);
            $table->decimal('saldo_usd', 10, 2);
            $table->enum('estado', ['activa', 'canjeada', 'vencida'])->default('activa');
            $table->date('fecha_emision');
            $table->date('fecha_expiracion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarjetas_regalo');
    }
};
