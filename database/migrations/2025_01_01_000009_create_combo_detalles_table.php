<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('combo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('componente_producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->decimal('porcentaje_descuento', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('combo_detalles');
    }
};
