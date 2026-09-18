# AGENTS.md

## Reglas de trabajo

### Merge a main (IMPORTANTE)
- **Nunca** hacer merge a `main` sin autorizacion explicita del usuario.
- Al terminar el trabajo en una rama (fix, feature, etc.) y haber pasado pruebas, presentarlo al usuario y esperar su visto bueno.
- Solo cuando el usuario confirme que el trabajo esta terminado y probado, se crea el PR y se hace el merge.
- Flujo normal: trabajo en rama → pint aislado → `php artisan test` → `npm run build` → push → PR con `gh` → merge con `gh pr merge --merge --delete-branch`.