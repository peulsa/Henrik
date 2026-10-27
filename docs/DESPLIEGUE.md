# Despliegue

Los navegadores cliente necesitan acceso a `unpkg.com` y `tile.openstreetmap.org` para cargar el mapa interactivo.

## Configuración

Crea el archivo `.env` directamente en el servidor y configura allí el entorno de producción, la URL, la base de datos y la integración con la plataforma principal. No subas ese archivo al repositorio.

## Preparación

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=HenrikSeeder --force
php artisan optimize
```

El servidor web debe apuntar a `public/`. `storage/` y `bootstrap/cache/` requieren permiso de escritura para el usuario de PHP.

Antes de actualizar, respaldar la base de datos. Después del despliegue, ejecutar `php artisan migrate --force`, `php artisan optimize` y `php artisan test`.
