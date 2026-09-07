<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonos_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained('creditos')->onDelete('cascade');
            $table->enum('metodo_pago', ['efectivo', 'punto', 'transferencia', 'tarjeta']);
            $table->decimal('monto_usd', 10, 2);
            $table->decimal('monto_ve', 10, 2);
            $table->decimal('tasa_bcv_aplicada', 10, 2)->nullable();
            $table->dateTime('fecha_abono')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abonos_credito');
    }
};
