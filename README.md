**Reglas básicas:**
- main siempre debe funcionar (nada roto)
- Nunca trabajes directamente en main
- Cada quien trabaja en su rama (mi-rama)
- Siempre actualízate antes de empezar
- Usa git status

**Flujo obligatorio:**
*Antes de hacer cambios*
1. Ir a main:
    git checkout main

2. Actualizar:
    git pull origin main

3. Volver a tu rama:
    git checkout mi-rama

4. Traer cambios de main:
    git merge main

*Durante los cambios*
1. Trabajar:
    git add .
    git commit -m "mensaje"

2. Subir tu rama:
    git push origin mi-rama

3. Crear Pull Request, Revisar y Merge hacia main

*Después del Merge en GitHub*
1. Sincronización final en main:
    git checkout main
    git pull origin main

2. Sincronización final en mi-rama:
    git checkout mi-rama
    git merge main
    git push origin mi-rama