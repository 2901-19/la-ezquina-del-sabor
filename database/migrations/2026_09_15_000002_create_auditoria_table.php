<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->string('accion');
            $table->string('entidad');
            $table->unsignedBigInteger('entidad_id');
            $table->json('datos_json')->nullable();
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();

            $table->index(['entidad', 'entidad_id']);
            $table->index('accion');
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
