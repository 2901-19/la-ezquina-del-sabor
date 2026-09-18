<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('indexar_costo_receta')->default(false)->after('receta_id');
        });

        DB::table('productos')
            ->whereNotNull('receta_id')
            ->update(['indexar_costo_receta' => true]);
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('indexar_costo_receta');
        });
    }
};
