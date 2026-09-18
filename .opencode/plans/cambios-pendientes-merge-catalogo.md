# Cambios pendientes de merge hacia main — rama `feature/ajustes-catalogo`

## Para qué es este documento
Este es un recordatorio, redactado en lenguaje natural, de los cambios que todavía no llegan a `main` y que están reunidos en la rama `feature/ajustes-catalogo`. Sirve para que, al revisar el PR, tengas a la vista el alcance completo en un solo lugar, sin necesidad de bucear entre commits. Los ocho cambios se describen por su objetivo y por la idea que resuelven; los títulos de los commits aparecen solo como referencia de dónde vive cada ajuste.

## Los cambios pendientes

### 1. La búsqueda de productos ya no rompe
El primer ajuste ataca el origen del problema que viste al usar la barra de búsqueda del catálogo. En la tabla de productos había columnas calculadas (el precio en bolívares, que se deriva en tiempo real de la tasa) y columnas booleanas (el estado activo o inactivo) que el motor de la tabla trataba como texto buscable. Al escribir cualquier palabra en la barra, el sistema generaba una consulta contra esas columnas y saltaba un error de base de datos. La corrección marca esas columnas como no buscables: la búsqueda se hace solo sobre las columnas reales y rellenables, que es lo que tiene sentido. El fix queda aislado en la capa de vista del datatable.

### 2. El producto puede decidir si el costo sale de la receta
La columna que permite a un producto tomar su costo automáticamente desde la receta vinculada. Hasta ahora, aunque un producto tuviera una receta, su costo se escribía a mano y no reaccionaba a lo que cambiara en esa receta. Este cambio agrega el interruptor "indexar costo de la receta", y además prepara la base: los productos que ya tenían una receta asociada quedan marcados para usar ese vínculo desde el momento de la migración, sin que tengas que volver a configurar nada.

### 3. La cascada del costo receta → producto
Cuando un producto está vinculado a una receta y tiene activo el interruptor de indexar costo, cualquier cambio que altere el costo de esa receta se propaga automáticamente: el producto actualiza su costo y, si trabaja por margen, también su precio de venta. Es decir, editar una receta con sus ingredientes ya no deja un producto con datos desactualizados; el vínculo se mantiene vivo. La actualización llega por cadena a las recetas que usan otras recetas como ingrediente, así el costo siempre queda consistente arriba en la jerarquía.

### 4. La cascada también responde a las materias primas
Del mismo modo, si lo que cambia es el costo unitario de una materia prima, la cadena reacciona igual: se recalculan las recetas que la usan y, desde ellas, los productos vinculados con el interruptor activo. Materia prima, receta y producto quedan así en una sola pieza: tocar el costo en cualquiera de los tres niveles actualiza el resto hacia abajo.

### 5. El formulario maneja el interruptor y corrige el cálculo del margen
En el backend, la creación y la edición de producto ya entienden el campo del interruptor y lo guardan en cada cambio. Además se corrige un bug latente en el cálculo del precio cuando el producto usa margen: la función de consulta de precio aplicaba el margen sobre un importe que ya incluía el margen, así que el resultado quedaba inflado (el margen se aplicaba dos veces). Ahora el margen se aplica una sola vez, partiendo del costo, y el precio final sale correcto.

### 6. El interruptor está en el formulario y se autocompleta
En la vista del formulario de producto aparece el interruptor "usar costo de la receta", al lado del selector de receta. Al elegir una receta se vincula de forma automática y, si el costo viene de la receta, el campo de costo se llena solo y se bloquea para edición manual; si lo desactivas, el costo vuelve a ser editable a mano. Todo esto se maneja también al editar un producto existente, respetando lo que ya esté guardado.

### 7. El texto del interruptor quedó en una sola línea y legible
Un ajuste fino de presentación: el texto de los interruptores de costo de receta y de visibilidad del producto ya no queda debajo del botón de palanca, sino escrito al lado, en la misma línea y en un solo bloque de texto legible. Es un cambio puramente visual para que el formulario se entienda de un vistazo.

### 8. Pruebas automáticas que aseguran todo lo anterior
La rama incorpora pruebas de características que cubren cada uno de los puntos: la búsqueda que ya no rompe, el interruptor encendido y apagado, la cascada desde la receta y desde la materia prima, y la migración que activa el vínculo para los productos ya existentes. Con esto, cualquier regresión a futuro quedaría detectada antes de llegar a producción.

## Estado al momento de escribir esto
Los ocho cambios están commiteados en `feature/ajustes-catalogo` y validados con la suite completa de pruebas en verde y la compilación de los assets sin errores. Ninguno de ellos ha sido llevado todavía a `main`; el propósito de este documento es que queden registrados de forma clara mientras se abre y revisa el PR.
