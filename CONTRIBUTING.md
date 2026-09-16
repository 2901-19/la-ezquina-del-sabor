# Guía de Contribución — La Esquina del Sabor

## Requisitos

- PHP 8.3+
- Node.js 18+
- PostgreSQL 17
- Composer
- npm

## Configuración del entorno

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan migrate:fresh --seed
```

## Ramas

Usamos el siguiente convencional para nombres de rama:

| Tipo | Prefijo | Ejemplo |
|------|---------|---------|
| Nueva funcionalidad | `feature/` | `feature/_venta-tarjetas` |
| Corrección de bug | `fix/` | `fix/login-redirect` |
| Mantenimiento/limpieza | `chore/` | `chore/pint-format` |
| Documentación | `docs/` | `docs/readme-actualizacion` |
| Refactorización | `refactor/` | `refactor/services-extraction` |

**Reglas:**
- Crear desde `main`
- Un PR por tarea/funcionalidad
- No hacer push directo a `main`

## Commits — Conventional Commits

Formato: `<tipo>(<ambito>): <descripción corta>`

### Tipos permitidos

| Tipo | Uso |
|------|-----|
| `feat` | Nueva funcionalidad |
| `fix` | Corrección de bug |
| `refactor` | Refactorización sin cambio de comportamiento |
| `chore` | Tareas de mantenimiento (migraciones, seeders, config) |
| `docs` | Documentación |
| `test` | Tests |
| `style` | Formato de código (Pint, ESLint) |

### Ejemplos

```
feat(comanda): agregar campo de nota al producto
fix(jornada): guardar monto_inicial al abrir jornada
chore(seeder): agregar permiso_rol seeder
refactor(stock): extraer logica a StockService
test(auth): agregar tests de login y logout
```

## Código

- Ejecutar `./vendor/bin/pint` antes de commitear
- Seguir los patrones existentes (Services, FormRequests, Controllers delgados)
- Principios SOLID: una responsabilidad por clase
- No duplicar lógica de negocio en controllers

## Pull Requests

1. Crear rama desde `main`
2. Hacer commits atómicos
3. Ejecutar `./vendor/bin/pint` y `php artisan test`
4. Crear PR con descripción clara del cambio
5. Esperar revisión antes de merge

## Estructura del proyecto

```
app/
├── Http/
│   ├── Controllers/    # Controllers delgados
│   ├── Middleware/      # Middleware de autorización
│   └── Requests/       # FormRequests (validación + autorización)
├── Models/             # Modelos Eloquent
├── Services/           # Lógica de negocio
├── Traits/             # Comportamientos reutilizables
└── Providers/          # Service providers
database/
├── migrations/         # Migraciones de BD
└── seeders/            # Seeders de datos
```
