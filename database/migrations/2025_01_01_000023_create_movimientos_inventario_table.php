<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comanda_id')->nullable()->constrained('comandas')->onDelete('set null');
            $table->foreignId('materia_prima_id')->constrained('materias_primas')->onDelete('cascade');
            $table->foreignId('compra_id')->nullable()->constrained('compras')->onDelete('set null');
            $table->dateTime('fecha_movimiento')->useCurrent();
            $table->decimal('costo_unitario', 10, 2);
            $table->decimal('cantidad_movimiento', 10, 2);
            $table->enum('tipo_movimiento', ['entrada', 'salida', 'merma']);
            $table->text('nota')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
