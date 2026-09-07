<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Catalogo\ProductoController;
use App\Http\Controllers\Catalogo\CategoriaController;
use App\Http\Controllers\Catalogo\RecetaController;
use App\Http\Controllers\Catalogo\ComboController;
use App\Http\Controllers\Inventario\MateriaPrimaController;
use App\Http\Controllers\Inventario\CompraController;
use App\Http\Controllers\Inventario\MermaController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Sistema\UsuarioController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Jornada\AperturaController;
use App\Http\Controllers\Jornada\CierreController;

Route::get('/', fn() => redirect('/login'));

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('catalogo/productos', ProductoController::class)
        ->except(['show', 'create', 'edit'])
        ->names('catalogo.productos');
    Route::get('/catalogo/productos/data', [ProductoController::class, 'data'])->name('catalogo.productos.data');

    Route::resource('catalogo/categorias', CategoriaController::class)
        ->except(['show', 'create', 'edit'])
        ->names('catalogo.categorias');
    Route::get('/catalogo/categorias/data', [CategoriaController::class, 'data'])->name('catalogo.categorias.data');

    Route::resource('catalogo/recetas', RecetaController::class)
        ->except(['show', 'create', 'edit'])
        ->names('catalogo.recetas');
    Route::get('/catalogo/recetas/data', [RecetaController::class, 'data'])->name('catalogo.recetas.data');

    Route::get('catalogo/combos', [ComboController::class, 'index'])->name('catalogo.combos.index');
    Route::get('catalogo/combos/data', [ComboController::class, 'data'])->name('catalogo.combos.data');
    Route::post('catalogo/combos', [ComboController::class, 'store'])->name('catalogo.combos.store');
    Route::put('catalogo/combos/{combo}', [ComboController::class, 'update'])->name('catalogo.combos.update');
    Route::delete('catalogo/combos/{combo}', [ComboController::class, 'destroy'])->name('catalogo.combos.destroy');

    Route::resource('inventario/materias-primas', MateriaPrimaController::class)
        ->except(['show', 'create', 'edit'])
        ->names('inventario.materias-primas');
    Route::get('/inventario/materias-primas/data', [MateriaPrimaController::class, 'data'])->name('inventario.materias-primas.data');

    Route::post('/inventario/compras', [CompraController::class, 'store'])->name('inventario.compras.store');
    Route::get('/inventario/compras/data', [CompraController::class, 'data'])->name('inventario.compras.data');

    Route::post('/inventario/mermas', [MermaController::class, 'store'])->name('inventario.mermas.store');
    Route::get('/inventario/mermas/data', [MermaController::class, 'data'])->name('inventario.mermas.data');

    Route::resource('clientes', ClienteController::class)->except(['show', 'create', 'edit']);
    Route::get('/clientes/data', [ClienteController::class, 'data'])->name('clientes.data');

    Route::resource('sistema/usuarios', UsuarioController::class)
        ->except(['show', 'create', 'edit'])
        ->names('sistema.usuarios');
    Route::get('/sistema/usuarios/data', [UsuarioController::class, 'data'])->name('sistema.usuarios.data');

    Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
    Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
    Route::get('/comandas/data', [ComandaController::class, 'data'])->name('comandas.data');
    Route::get('/cocina', [ComandaController::class, 'cocina'])->name('cocina.index');

    Route::get('/creditos', [CreditoController::class, 'index'])->name('creditos.index');
    Route::get('/creditos/data', [CreditoController::class, 'data'])->name('creditos.data');

    Route::get('/jornada/apertura', [AperturaController::class, 'show'])->name('jornada.apertura');
    Route::post('/jornada/abrir', [AperturaController::class, 'abrir'])->name('jornada.abrir');
    Route::get('/jornada/cierre', [CierreController::class, 'show'])->name('jornada.cierre');

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportar'])->name('reportes.exportar')->middleware('throttle:exportar');

    Route::get('/sistema/configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'index'])->name('sistema.configuracion');
    Route::post('/sistema/configuracion', [\App\Http\Controllers\ConfiguracionController::class, 'update'])->name('sistema.configuracion.update');
});
