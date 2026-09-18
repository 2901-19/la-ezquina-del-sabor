# Plan: Módulo de Recetas — Corrección y Completado

## Objetivo
Corregir todos los bugs del módulo de recetas y completar la funcionalidad de gestión de ingredientes (materias primas + sub-recetas) en un solo modal, incluyendo cálculo automático de costo, tests y seeders corregidos.

## Estado actual
- Controller: 4 métodos (index, data, store, update, destroy) — **sin show()**
- Vista: modal con 3 campos (nombre, descripcion, costo_total_usd) — **sin UI de ingredientes**
- Edit/Delete: **rotos** (sin ruta show, handler JS no encuentra URL)
- Tests: **0**
- Seeders: IDs hardcodeados, costo_total_usd no concuerda con ingredientes
- migración receta_detalles: **sin FK** en materia_prima_id

---

## Fase 0 — Fix global de JS (app.js)

> Afecta a TODOS los módulos, no solo recetas. Se hace primero porque desbloquea edit/delete.

### Tarea 0.1: Fix delete handler en `resources/js/app.js`
**Archivo:** `resources/js/app.js:290-302`
**Problema:** `baseUrl = btn.closest('.ajax-form')` siempre es `''` porque los botones de fila están en DataTables, no en un form.
**Solución:** Agregar `data-url` al botón delete en cada controller `data()`, y leerlo en el JS.

Cambios en `app.js` (handler de `data-act="borrar"`):
```js
// ANTES (roto):
var baseUrl = btn.closest('.ajax-form') ? btn.closest('.ajax-form').getAttribute('action') : '';
if (baseUrl && id) { confirmarBorrar(baseUrl + '/' + id); }

// DESPUÉS:
var deleteUrl = btn.getAttribute('data-url');
if (deleteUrl) { confirmarBorrar(deleteUrl); }
```

### Tarea 0.2: Agregar `data-url` al botón de borrar en `RecetaController::data()`
**Archivo:** `app/Http/Controllers/Catalogo/RecetaController.php:19-26`
**Cambio:** Agregar `data-url="{{ route('catalogo.recetas.destroy', $receta->id) }}"` al botón delete.

> NOTA: Esto se replicará en otros controllers (Categoria, Producto, Combo, MateriaPrima) como paso separado. Por ahora solo recetas.

---

## Fase 1 — Backend: Rutas y Controller

### Tarea 1.1: Agregar ruta `show` para recetas
**Archivo:** `routes/web.php:50-52`
**Cambio:** Remover `'show'` de la lista `except`:
```php
// ANTES:
Route::resource('catalogo/recetas', RecetaController::class)
    ->except(['show', 'create', 'edit', 'index'])

// DESPUÉS:
Route::resource('catalogo/recetas', RecetaController::class)
    ->except(['create', 'edit', 'index'])
```

### Tarea 1.2: Agregar método `show()` a RecetaController
**Archivo:** `app/Http/Controllers/Catalogo/RecetaController.php`
**Nuevo método:**
```php
public function show(Receta $receta)
{
    $receta->load([
        'recetaDetalles.materiaPrima',
        'recetaDetalles.recetaBase',
    ]);

    return response()->json([
        'success' => true,
        'data' => $receta,
    ]);
}
```
**Carga eager:** `recetaDetalles.materiaPrima` (para mostrar nombre de materia prima) y `recetaDetalles.recetaBase` (para sub-recetas).

### Tarea 1.3: Modificar `store()` para aceptar ingredientes anidados
**Archivo:** `app/Http/Controllers/Catalogo/RecetaController.php`
**Cambio:** Recibir `detalles` como array anidado y crear RecetaDetalle dentro de un DB::transaction.

```php
public function store(StoreRecetaRequest $request)
{
    return DB::transaction(function () use ($request) {
        $receta = Receta::create($request->validated());

        if ($request->has('detalles')) {
            foreach ($request->detalles as $detalle) {
                $receta->recetaDetalles()->create([
                    'materia_prima_id' => $detalle['materia_prima_id'] ?? null,
                    'receta_base_id' => $detalle['receta_base_id'] ?? null,
                    'cantidad_requerida' => $detalle['cantidad_requerida'],
                ]);
            }
        }

        $receta->recalcularCosto();

        return response()->json([
            'success' => true,
            'message' => 'Receta creada exitosamente.',
            'data' => $receta->load('recetaDetalles'),
        ]);
    });
}
```

### Tarea 1.4: Modificar `update()` para reemplazar ingredientes
**Archivo:** `app/Http/Controllers/Catalogo/RecetaController.php`
**Cambio:** Dentro de DB::transaction, eliminar detalles existentes y recrear.

```php
public function update(UpdateRecetaRequest $request, Receta $receta)
{
    return DB::transaction(function () use ($request, $receta) {
        $receta->update($request->validated());

        if ($request->has('detalles')) {
            $receta->recetaDetalles()->delete();
            foreach ($request->detalles as $detalle) {
                $receta->recetaDetalles()->create([
                    'materia_prima_id' => $detalle['materia_prima_id'] ?? null,
                    'receta_base_id' => $detalle['receta_base_id'] ?? null,
                    'cantidad_requerida' => $detalle['cantidad_requerida'],
                ]);
            }
        }

        $receta->recalcularCosto();

        return response()->json([
            'success' => true,
            'message' => 'Receta actualizada exitosamente.',
            'data' => $receta->load('recetaDetalles'),
        ]);
    });
}
```

### Tarea 1.5: Agregar método `recalcularCosto()` al modelo Receta
**Archivo:** `app/Models/Receta.php`
**Nuevo método:**
```php
public function recalcularCosto(): void
{
    $costo = $this->recetaDetalles->sum(function ($detalle) {
        if ($detalle->materia_prima_id && $detalle->materiaPrima) {
            return $detalle->materiaPrima->costo_unitario_usd * $detalle->cantidad_requerida;
        }
        if ($detalle->receta_base_id && $detalle->recetaBase) {
            return $detalle->recetaBase->costo_total_usd * $detalle->cantidad_requerida;
        }
        return 0;
    });

    $this->update(['costo_total_usd' => round($costo, 2)]);
}
```

---

## Fase 2 — Backend: Validación (FormRequests)

### Tarea 2.1: Actualizar `StoreRecetaRequest`
**Archivo:** `app/Http/Requests/Catalogo/StoreRegetaRequest.php`
**Cambios:**
- Agregar `unique:recetas,nombre` al campo `nombre`
- Agregar reglas para `detalles` array (nullable, array, min:1)
- Agregar reglas para cada `detalles.*`:
  - `materia_prima_id`: nullable, integer, exists:materias_primas,id (requerido si no hay receta_base_id)
  - `receta_base_id`: nullable, integer, exists:recetas,id (requerido si no hay materia_prima_id, diferente al receta actual)
  - `cantidad_requerida`: required, numeric, min:0.01
- Validar que al menos uno de `materia_prima_id` o `receta_base_id` esté presente

### Tarea 2.2: Actualizar `UpdateRecetaRequest`
**Archivo:** `app/Http/Requests/Catalogo/UpdateRecetaRequest.php`
**Cambios:** Misma estructura que Store, pero con `unique:recetas,nombre,{id}` en nombre.

---

## Fase 3 — Backend: Migración

### Tarea 3.1: Agregar FK a `materia_prima_id` en `receta_detalles`
**Archivo nueva migración:** `database/migrations/2026_09_16_000001_add_foreign_key_to_receta_detalles.php`
```php
Schema::table('receta_detalles', function (Blueprint $table) {
    $table->foreign('materia_prima_id')
          ->references('id')
          ->on('materias_primas')
          ->nullOnDelete();
});
```
> `nullOnDelete`: si se borra una materia prima, el detalle queda sin materia prima (no se cascada la receta completa).

---

## Fase 4 — Frontend: Vista de recetas con ingredientes

### Tarea 4.1: Rediseñar modal de recetas
**Archivo:** `resources/views/catalogo/recetas.blade.php`
**Estructura del modal (modal-lg o modal-xl):**

```
┌─────────────────────────────────────────────────┐
│ Nueva receta / Editar receta                [X] │
├─────────────────────────────────────────────────┤
│                                                 │
│  ┌─── Datos generales ───────────────────────┐  │
│  │ Nombre *    [________________________]     │  │
│  │ Descripción [________________________]     │  │
│  │                                               │  │
│  │ Costo total: $ 0.00 (calculado automáticamente)│
│  └───────────────────────────────────────────┘  │
│                                                 │
│  ┌─── Ingredientes ─────────────────────────┐  │
│  │ [ + Agregar ingrediente ]                  │  │
│  │                                             │  │
│  │ ┌──────┬──────────┬──────────┬───────┬──┐ │  │
│  │ │ Tipo │ Material │ Cantidad │ Costo │ X│ │  │
│  │ ├──────┼──────────┼──────────┼───────┼──┤ │  │
│  │ │ MP ▼ │ Carne    │ 0.15     │ $0.45 │ 🗑│ │  │
│  │ │ SR ▼ │ Salsa X  │ 0.02     │ $0.10 │ 🗑│ │  │
│  │ └──────┴──────────┴──────────┴───────┴──┘ │  │
│  │                                             │  │
│  │ Costo calculado: $0.55                     │  │
│  └───────────────────────────────────────────┘  │
│                                                 │
│          [Cancelar]  [Guardar receta]           │
└─────────────────────────────────────────────────┘
```

**Campos del form (ocultos, para envío):**
```html
<input type="hidden" name="detalles[0][tipo]" value="materia_prima">
<input type="hidden" name="detalles[0][materia_prima_id]" value="1">
<input type="hidden" name="detalles[0][receta_base_id]" value="">
<input type="hidden" name="detalles[0][cantidad_requerida]" value="0.15">
<!-- ... repite por cada fila ... -->
```

### Tarea 4.2: JS de gestión de ingredientes
**Archivo:** `resources/views/catalogo/recetas.blade.php` (sección `@push('scripts')`)

Funciones necesarias:
1. **`agregarFila()`**: Agrega una nueva fila de ingrediente al DOM con:
   - Select de tipo (Materia Prima / Sub-receta)
   - Select dinámico de material (cambia según tipo)
   - Input de cantidad
   - Costo calculado (readonly)
   - Botón eliminar

2. **`eliminarFila(btn)`**: Elimina la fila y recalcula el costo total.

3. **`cambiarTipo(fila, tipo)`**: Cuando cambia el select tipo, carga las opciones del select de material:
   - Si "Materia Prima": fetch `/inventario/materias-primas/data` o usar datos precargados
   - Si "Sub-receta": usar las recetas disponibles (excluyendo la actual en modo edición)

4. **`calcularCostoFila(fila)`**: `costo_unitario * cantidad` → muestra en la columna costo.

5. **`recalcularCostoTotal()`**: Suma todos los costos de fila → actualiza el campo `costo_total_usd` y el display.

6. **`cargarDetalleReceta(receta)`**: En modo edición, recibe los `recetaDetalles` del fetch `show()` y crea las filas correspondientes.

**Datos precargados:** El controller `index()` ya pasa `$recetas` al view. Se necesita agregar `$materiasPrimas`:
```php
public function index(Request $request)
{
    $materiasPrimas = MateriaPrima::where('activo', true)->orderBy('nombre')->get();
    $recetas = Receta::orderBy('nombre')->get();
    return view('catalogo.recetas', compact('materiasPrimas', 'recetas'));
}
```

### Tarea 4.3: Fix delete handler en la vista
**Archivo:** `resources/views/catalogo/recetas.blade.php`
**Cambio:** En la columna acciones del DataTable, agregar `data-url` al botón delete:
```php
return '
    <div class="row-actions">
        <button class="icon-btn" data-act="editar" data-id="'.$receta->id.'" title="Editar">
            <i class="bi bi-pencil"></i>
        </button>
        <button class="icon-btn del" data-act="borrar" data-id="'.$receta->id.'"
                data-url="'.route('catalogo.recetas.destroy', $receta->id).'" title="Eliminar">
            <i class="bi bi-trash3"></i>
        </button>
    </div>
';
```

### Tarea 4.4: Fix edit handler — precargar ingredientes
**Archivo:** `resources/js/app.js` (handler de `data-act="editar"`)
**Cambio:** Después de recibir `data.data` del fetch, si hay `recetaDetalles`, llamar a una función global `cargarDetalleReceta(data.data)` que esté definida en la vista de recetas.

En `app.js`, agregar después del mapeo de campos:
```js
// Después deObject.keys(record).forEach(...)
if (typeof window.cargarDetalleReceta === 'function') {
    window.cargarDetalleReceta(record);
}
```

### Tarea 4.5: Fix create-modal reset para limpiar ingredientes
**Archivo:** `resources/views/catalogo/recetas.blade.php`
**Cambio:** Cuando se abre el modal en modo "Nueva receta", limpiar el contenedor de filas de ingredientes.

En el handler de `[data-bs-target]` del modal de recetas:
```js
document.getElementById('modalReceta').addEventListener('show.bs.modal', function() {
    // Si es creación (no edición), limpiar ingredientes
    var form = this.querySelector('.ajax-form');
    if (form.getAttribute('action').indexOf('/store') !== -1) {
        document.getElementById('ingredientesBody').innerHTML = '';
        recalcularCostoTotal();
    }
});
```

---

## Fase 5 — Seeders

### Tarea 5.1: Hacer RecetaSeeder idempotente
**Archivo:** `database/seeders/RecetaSeeder.php`
**Cambio:** Usar `firstOrCreate` en vez de `create`:
```php
foreach ($recetas as $r) {
    Receta::firstOrCreate(['nombre' => $r['nombre']], $r);
}
```

### Tarea 5.2: Hacer RecetaDetalleSeeder idempotente
**Archivo:** `database/seeders/RecetaDetalleSeeder.php`
**Cambio:** Resolver por nombre en vez de IDs hardcodeados:
```php
$carne = MateriaPrima::where('nombre', 'Carne molida')->first();
$hamburguesa = Receta::where('nombre', 'Receta Hamburguesa Esquina')->first();
RecetaDetalle::firstOrCreate(
    ['receta_id' => $hamburguesa->id, 'materia_prima_id' => $carne->id],
    ['cantidad_requerida' => 0.15]
);
```

### Tarea 5.3: Recalcular costo_total_usd post-seed
**Archivo:** `database/seeders/RecetaSeeder.php`
**Cambio:** Al final del seeder, recalcular costo de cada receta:
```php
Receta::all()->each->recalcularCosto();
```

---

## Fase 6 — Tests

### Tarea 6.1: Crear `tests/Feature/Catalogo/RecetaCrudTest.php`
**Patrón:** Espejo de `CategoriaCrudTest.php` pero expandido.

Tests a implementar:
1. `test_listar_recetas` — GET /catalogo/recetas → 200
2. `test_data_recetas` — GET /catalogo/recetas/data → 200 + JSON structure
3. `test_crear_receta_sin_ingredientes` — POST con nombre → 200 + DB has
4. `test_crear_receta_con_ingredientes` — POST con detalles array → 200 + receta_detalles has
5. `test_crear_receta_con_sub_receta` — POST con receta_base_id → 200
6. `test_crear_receta_nombre_duplicado` — POST con nombre existente → 422
7. `test_editar_receta` — GET show + PUT update → 200
8. `test_editar_receta_reemplazar_ingredientes` — PUT con nuevo array de detalles → receta_detalles actualizado
9. `test_eliminar_receta` — DELETE → 200 + receta_detalles cascade
10. `test_eliminar_receta_con_producto_vinculado` — DELETE → receta_id se pone null en productos
11. `test_costo_total_se_calcula_automaticamente` — crear receta con ingredientes, verificar costo_total_usd
12. `test_permiso_requerido` — usuario sin editar_catalogo → 403
13. `test_show_receta` — GET /catalogo/recetas/{id} → 200 + detalles included
14. `test_detalle_requiere_materia_prima_o_sub_receta` — POST sin ninguno → 422

### Tarea 6.2: Crear factories
**Archivos nuevos:**
- `database/factories/RecetaFactory.php`
- `database/factories/RecetaDetalleFactory.php`
- `database/factories/MateriaPrimaFactory.php`

---

## Fase 7 — Pulido y validación cruzada

### Tarea 7.1: Verificar que ProductoController funciona con recetas actualizadas
- `ProductoController::index()` pasa `$recetas` al view → verificar que el dropdown muestra recetas correctamente
- `ProductoSeeder` usa `Receta::find($id)` → verificar que el seeder sigue funcionando

### Tarea 7.2: Verificar cascade delete
- Borrar una receta → productos con `receta_id` → `SET NULL` (OK)
- Borrar una receta → `receta_detalles` → `CASCADE` (OK)
- Borrar una materia prima que está en un receta_detalle → `SET NULL` (nueva FK)

### Tarea 7.3: Correr tests
```bash
vendor\bin\pint --test
php artisan test --filter=RecetaCrudTest
```

---

## Resumen de archivos a crear/modificar

| Archivo | Acción |
|---------|--------|
| `resources/js/app.js` | Modificar (fix delete handler + delegate para cargarDetalleReceta) |
| `routes/web.php` | Modificar (agregar show a except list) |
| `app/Http/Controllers/Catalogo/RecetaController.php` | Modificar (agregar show, modificar store/update, agregar import DB) |
| `app/Models/Receta.php` | Modificar (agregar recalcularCosto) |
| `app/Http/Requests/Catalogo/StoreRecetaRequest.php` | Modificar (unique + detalles rules) |
| `app/Http/Requests/Catalogo/UpdateRecetaRequest.php` | Modificar (unique exception + detalles rules) |
| `resources/views/catalogo/recetas.blade.php` | Rediseñar (modal con ingredientes + JS) |
| `database/migrations/2026_09_16_000001_add_foreign_key_to_receta_detalles.php` | Nuevo |
| `database/seeders/RecetaSeeder.php` | Modificar (idempotente + recalcular costo) |
| `database/seeders/RecetaDetalleSeeder.php` | Modificar (resolver por nombre) |
| `tests/Feature/Catalogo/RecetaCrudTest.php` | Nuevo |
| `database/factories/RecetaFactory.php` | Nuevo |
| `database/factories/RecetaDetalleFactory.php` | Nuevo |
| `database/factories/MateriaPrimaFactory.php` | Nuevo |

---

## Orden de ejecución sugerido

1. **Fase 0** (JS global) → desbloquea edit/delete para todos
2. **Fase 3** (migración FK) → integrity antes de datos
3. **Fase 1** (controller + routes) → backend funcional
4. **Fase 2** (FormRequests) → validación
5. **Fase 5** (seeders) → datos consistentes
6. **Fase 4** (vista + JS) → UI funcional
7. **Fase 6** (tests) → verificación
8. **Fase 7** (pulido) → validación cruzada

---

## Estimación

| Fase | Tareas | Tiempo estimado |
|------|--------|----------------|
| Fase 0 | 2 | 30 min |
| Fase 1 | 5 | 1.5 h |
| Fase 2 | 2 | 45 min |
| Fase 3 | 1 | 15 min |
| Fase 4 | 5 | 3 h |
| Fase 5 | 3 | 45 min |
| Fase 6 | 2 | 1.5 h |
| Fase 7 | 3 | 45 min |
| **Total** | **23** | **~9 h** |
