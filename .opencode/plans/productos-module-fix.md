# Plan: Módulo de Productos — Corrección y Completado

## Objetivo
Corregir los bugs del módulo de productos (500 al guardar, editar/ver/borrar rotos, campos fantasma imagen/descripción) y completarlo: persistir costo_usd para margen, cálculo del precio por margen, bloqueo de borrado con referencias y modal de detalle.

## Estado actual
- `store()` → **500**: `PrecioService::getPrecio()` recibe `(object)` pero exige `Producto $producto` → TypeError. El resultado además no se usa.
- **Editar roto**: la ruta `catalogo.productos.show` no existe (`except(['show', ...])`).
- **Botón ver roto**: el handler `data-act="ver"` en app.js:carga `/comandas/{id}/show` (hardcodeado).
- **Botón borrar muerto**: `data()` genera botón sin `data-url` (app.js:lo requiere).
- **Campos fantasma**: `imagen`/`descripcion` enviados desde el modal pero no existen en la tabla `productos` → se pierden silenciosamente.
- Riesgos: ordenar por columna calculada `precio_bs` rompe el server-side; sin validación `unique` de nombre; `costo_usd` no se persistía (margen sin costo).

## Decisiones (aprobadas por el usuario)
1. Quitar campos `imagen`/`descripcion` del modal (no existen en BD).
2. Persistir `costo_usd` y calcular `precio_usd = costo_usd + (costo_usd × margen / 100)` para `tipo_precio=margen`.
3. Bloquear borrado con mensaje (JSON 409) si el producto está en `comanda_detalles.producto_id` o `combo_detalles.componente_producto_id`.
4. Crear modal de detalle de producto con ruta `show()`.

---

## Fase 0 — Rutas y Backend

### Tarea 0.1: Activar ruta `show`
**Archivo:** `routes/web.php:45-47`
Quitar `'show'` del `except` del resource de productos.

### Tarea 0.2: Fix `data()` — botones con data-url y ver-producto
**Archivo:** `app/Http/Controllers/Catalogo/ProductoController.php::data()`
- Botón ver: `data-act="ver-producto"` + `data-url=route('catalogo.productos.show')`.
- Botón borrar: agregar `data-url=route('catalogo.productos.destroy')` (app.js lo requiere).

### Tarea 0.3: Fix store() (500) y cálculo por margen
**Archivo:** `app/Http/Controllers/Catalogo/ProductoController.php`
- Eliminar `PrecioService::getPrecio()` (roto). Usar `PrecioService::calcularPrecioMargen(costo, margen)` dentro de un helper privado `calcularPrecio()`.
- Persistir `costo_usd` (null si tipo=definido) y `precio_usd` calculado.

### Tarea 0.4: Fix update() y destroy() con bloqueo
- `update()`: aplicar el mismo cálculo de precio y persistir costo_usd.
- `destroy()`: verificar `comandaDetalles()->exists()` y `comboComponentes()->exists()`; si hay referencias → JSON 409 con mensaje; si no → delete.

### Tarea 0.5: Fix show() con relaciones y precio_bs
- Eager load `categoria`, `receta`.
- Inyectar `precio_bs` calculado con `Configuracion::obtener('tasa_bcv', 818)`.

---

## Fase 1 — Migración y Modelo

### Tarea 1.1: Migración `add_costo_usd_to_productos_table`
**Archivo nuevo:** `database/migrations/2026_09_18_000001_add_costo_usd_to_productos_table.php`
`decimal('costo_usd', 10, 2)->nullable()` tras `margen_ganancia`.

### Tarea 1.2: Modelo Producto
- `costo_usd` en `$fillable`.
- Casts: `costo_usd => decimal:2`.

---

## Fase 2 — FormRequests

### Tarea 2.1: `StoreProductoRequest`
- `nombre`: required + `unique:productos,nombre`.
- `categoria_id`: required exists.
- `tipo_precio`: in:margen,definido.
- `costo_usd`: `required_if:tipo_precio,margen|numeric|min:0`.
- `margen_ganancia`: `required_if:tipo_precio,margen|numeric|min:0|max:200`.
- `precio_usd`: `required_if:tipo_precio,definido|numeric|min:0`.

### Tarea 2.2: `UpdateProductoRequest`
Igual, pero `unique(...)->ignore($this->producto)`.

---

## Fase 3 — Frontend

### Tarea 3.1: Vista `productos.blade.php`
- Quitar campos `imagen` y `descripcion` del modal.
- Renombrar `name="costo"` → `name="costo_usd"` (id `fCosto` se mantiene).
- Columna `precio_bs` → `orderable:false` (es calculada).
- Agregar `modalVerProducto` (detalle) al final.

### Tarea 3.2: JS de la vista
- Handler `data-act="ver-producto"`: fetch URL → rellenar modal → `openModal('modalVerProducto')`.
- `window.cargarDetalleProducto(record)`: en edición, `setTipoPrecio(record.tipo_precio)` + dispatch `input` en `fCosto`/`fMargen` para recalcular displays.

### Tarea 3.3: app.js
- Invocar `window.cargarDetalleProducto(record)` tras el mapeo de campos, junto a `cargarDetalleReceta`.

---

## Fase 4 — Tests

### Tarea 4.1: Ampliar `tests/Feature/Catalogo/ProductoCrudTest.php`
1. `test_listar_productos`
2. `test_crear_producto_margen_calcula_precio` (costo 4.00 + 35% → 5.40)
3. `test_crear_producto_definido` (costo_usd null)
4. `test_validar_nombre_duplicado` (422)
5. `test_validar_margen_sin_costo` (422)
6. `test_editar_producto`
7. `test_show_incluye_relaciones_y_precio_bs`
8. `test_eliminar_producto_sin_referencias` (200)
9. `test_eliminar_producto_usado_en_comanda_se_bloquea` (409)
10. `test_eliminar_producto_componente_de_combo_se_bloquea` (409)
11. `test_permiso_requerido_para_crear` (403)
12. `test_data_filtra_por_categoria`
13. `test_data_filtra_por_estado`

> NOTA: no usar id literal `1` para categorías — en pgsql los IDs no se resetean entre tests. Capturar la instancia creada.

---

## Fase 5 — Seeders y validación

### Tarea 5.1: `ProductoSeeder`
- Para `tipo_precio=margen`, persistir `costo_usd = receta->costo_total_usd`.

### Tarea 5.2: Pint aislado + tests + build
```bash
vendor\bin\pint <archivos tocados>
php artisan test
npm run build
```

---

## Resumen de archivos

| Archivo | Acción |
|---------|--------|
| `app/Http/Controllers/Catalogo/ProductoController.php` | Modificar (fix store/update/show/destroy/data) |
| `app/Models/Producto.php` | Modificar (costo_usd fillable + cast) |
| `app/Http/Requests/Catalogo/StoreProductoRequest.php` | Modificar (unique + reglas margen/definido) |
| `app/Http/Requests/Catalogo/UpdateProductoRequest.php` | Modificar (unique ignore + reglas) |
| `resources/views/catalogo/productos.blade.php` | Modificar (quitar imagen/descripcion, costo_usd, modalVerProducto, handlers) |
| `resources/js/app.js` | Modificar (hook cargarDetalleProducto) |
| `routes/web.php` | Modificar (activar ruta show) |
| `database/migrations/2026_09_18_000001_add_costo_usd_to_productos_table.php` | Nuevo |
| `database/seeders/ProductoSeeder.php` | Modificar (costo_usd para margen) |
| `tests/Feature/Catalogo/ProductoCrudTest.php` | Ampliar (CRUD completo) |

---

## Orden de ejecución

1. Fase 0 (rutas + backend)
2. Fase 1 (migración + modelo)
3. Fase 2 (FormRequests)
4. Fase 3 (frontend)
5. Fase 4 (tests)
6. Fase 5 (seeders + pint + tests + build)