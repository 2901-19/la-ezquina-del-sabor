<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->constrained('comandas')->onDelete('cascade');
            $table->foreignId('tarjeta_regalo_id')->nullable()->constrained('tarjetas_regalo')->onDelete('set null');
            $table->enum('metodo_pago', ['efectivo', 'punto', 'transferencia', 'tarjeta', 'credito']);
            $table->decimal('monto_usd', 10, 2);
            $table->decimal('monto_ve', 10, 2);
            $table->dateTime('fecha_pago')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
