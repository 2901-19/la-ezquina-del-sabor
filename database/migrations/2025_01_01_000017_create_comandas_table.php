<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jornada_id')->constrained('jornadas')->onDelete('cascade');
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
            $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
            $table->decimal('tasa_bcv_aplicada', 10, 2);
            $table->string('numero_correlativo_diario');
            $table->string('nombre_cliente_temporal')->nullable();
            $table->text('descripcion_cliente')->nullable();
            $table->string('telefono_delivery')->nullable();
            $table->enum('estado_comanda', ['montar', 'entrega', 'cobrar', 'cerrada'])->default('montar');
            $table->decimal('total_usd', 10, 2)->default(0);
            $table->decimal('total_ve', 10, 2)->default(0);
            $table->text('notas_generales')->nullable();
            $table->dateTime('fecha_creacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comandas');
    }
};
