<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Catalogo\CategoriaController;
use App\Http\Controllers\Catalogo\ComboController;
use App\Http\Controllers\Catalogo\ProductoController;
use App\Http\Controllers\Catalogo\RecetaController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\ComandaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Inventario\CompraController;
use App\Http\Controllers\Inventario\MateriaPrimaController;
use App\Http\Controllers\Inventario\MermaController;
use App\Http\Controllers\Jornada\AperturaController;
use App\Http\Controllers\Jornada\CierreController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Sistema\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Catálogo — ver_catalogo (lectura) + editar_catalogo (escritura)
    Route::middleware('permiso:ver_catalogo')->group(function () {
        Route::get('/catalogo/productos', [ProductoController::class, 'index'])->name('catalogo.productos.index');
        Route::get('/catalogo/productos/data', [ProductoController::class, 'data'])->name('catalogo.productos.data');
        Route::get('/catalogo/categorias', [CategoriaController::class, 'index'])->name('catalogo.categorias.index');
        Route::get('/catalogo/categorias/data', [CategoriaController::class, 'data'])->name('catalogo.categorias.data');
        Route::get('/catalogo/recetas', [RecetaController::class, 'index'])->name('catalogo.recetas.index');
        Route::get('/catalogo/recetas/data', [RecetaController::class, 'data'])->name('catalogo.recetas.data');
        Route::get('catalogo/combos', [ComboController::class, 'index'])->name('catalogo.combos.index');
        Route::get('catalogo/combos/data', [ComboController::class, 'data'])->name('catalogo.combos.data');
    });

    Route::middleware('permiso:editar_catalogo')->group(function () {
        Route::resource('catalogo/productos', ProductoController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('catalogo.productos');
        Route::resource('catalogo/categorias', CategoriaController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('catalogo.categorias');
        Route::resource('catalogo/recetas', RecetaController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('catalogo.recetas');
        Route::post('catalogo/combos', [ComboController::class, 'store'])->name('catalogo.combos.store');
        Route::put('catalogo/combos/{combo}', [ComboController::class, 'update'])->name('catalogo.combos.update');
        Route::delete('catalogo/combos/{combo}', [ComboController::class, 'destroy'])->name('catalogo.combos.destroy');
    });

    // Inventario
    Route::middleware('permiso:ver_inventario')->group(function () {
        Route::get('/inventario/materias-primas', [MateriaPrimaController::class, 'index'])->name('inventario.materias-primas.index');
        Route::get('/inventario/materias-primas/data', [MateriaPrimaController::class, 'data'])->name('inventario.materias-primas.data');
        Route::get('/inventario/compras/data', [CompraController::class, 'data'])->name('inventario.compras.data');
        Route::get('/inventario/mermas/data', [MermaController::class, 'data'])->name('inventario.mermas.data');
    });

    Route::middleware('permiso:editar_inventario')->group(function () {
        Route::resource('inventario/materias-primas', MateriaPrimaController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('inventario.materias-primas');
        Route::post('/inventario/compras', [CompraController::class, 'store'])->name('inventario.compras.store');
        Route::post('/inventario/mermas', [MermaController::class, 'store'])->name('inventario.mermas.store');
    });

    // Clientes
    Route::middleware('permiso:ver_clientes')->group(function () {
        Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
        Route::get('/clientes/data', [ClienteController::class, 'data'])->name('clientes.data');
    });

    Route::middleware('permiso:editar_clientes')->group(function () {
        Route::resource('clientes', ClienteController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('clientes');
    });

    // Usuarios
    Route::middleware('permiso:ver_usuarios')->group(function () {
        Route::get('/sistema/usuarios', [UsuarioController::class, 'index'])->name('sistema.usuarios.index');
        Route::get('/sistema/usuarios/data', [UsuarioController::class, 'data'])->name('sistema.usuarios.data');
    });

    Route::middleware('permiso:gestionar_usuarios')->group(function () {
        Route::resource('sistema/usuarios', UsuarioController::class)
            ->except(['show', 'create', 'edit', 'index'])
            ->names('sistema.usuarios');
    });

    // Comandas
    Route::middleware('permiso:crear_comanda')->group(function () {
        Route::get('/comandas', [ComandaController::class, 'index'])->name('comandas.index');
        Route::post('/comandas', [ComandaController::class, 'store'])->name('comandas.store');
        Route::get('/comandas/data', [ComandaController::class, 'data'])->name('comandas.data');
        Route::get('/comandas/{comanda}/show', [ComandaController::class, 'show'])->name('comandas.show');
    });

    Route::get('/cocina', [ComandaController::class, 'cocina'])
        ->middleware('permiso:marcar_entrega')
        ->name('cocina.index');

    // Créditos
    Route::middleware('permiso:gestionar_creditos')->group(function () {
        Route::get('/creditos', [CreditoController::class, 'index'])->name('creditos.index');
        Route::get('/creditos/data', [CreditoController::class, 'data'])->name('creditos.data');
    });

    // Jornada
    Route::middleware('permiso:abrir_jornada')->group(function () {
        Route::get('/jornada/apertura', [AperturaController::class, 'show'])->name('jornada.apertura');
        Route::post('/jornada/abrir', [AperturaController::class, 'abrir'])->name('jornada.abrir');
    });

    Route::middleware('permiso:cierre_jornada')->group(function () {
        Route::get('/jornada/cierre', [CierreController::class, 'show'])->name('jornada.cierre');
        Route::post('/jornada/cerrar', [CierreController::class, 'cerrar'])->name('jornada.cerrar');
    });

    // Reportes
    Route::middleware('permiso:ver_reportes')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
    });

    Route::middleware('permiso:exportar_reportes')->group(function () {
        Route::get('/reportes/exportar/{tipo}', [ReporteController::class, 'exportar'])
            ->middleware('throttle:exportar')
            ->name('reportes.exportar');
    });

    // Configuración
    Route::middleware('permiso:configurar')->group(function () {
        Route::get('/sistema/configuracion', [ConfiguracionController::class, 'index'])->name('sistema.configuracion');
        Route::post('/sistema/configuracion', [ConfiguracionController::class, 'update'])->name('sistema.configuracion.update');
    });
});
