# Despliegue

## Variables necesarias

```dotenv
APP_NAME=Henrik
APP_ENV=production
APP_DEBUG=false
APP_URL=https://inventario.ejemplo.cl
APP_TIMEZONE=America/Santiago

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=henrik
DB_USERNAME=henrik
DB_PASSWORD=una-clave-segura

HENRIK_PLATFORM_SHARED_SECRET=un-secreto-largo-y-aleatorio
HENRIK_ALLOW_LOCAL_IDENTITY=false
```

## Preparación

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=HenrikSeeder --force
php artisan optimize
```

El servidor web debe apuntar a `public/`. `storage/` y `bootstrap/cache/` requieren permiso de escritura para el usuario de PHP.

## Integración segura

1. Configurar el mismo secreto en la plataforma y en Henrik.
2. Restringir Henrik para que solo reciba tráfico desde el proxy o la plataforma principal.
3. Eliminar cualquier encabezado `X-Henrik-*` aportado por el navegador y generar encabezados nuevos después de autenticar al usuario.
4. Verificar una solicitud con un usuario de prueba y confirmar su correo en la auditoría.

Antes de actualizar, respaldar la base de datos. Después del despliegue, ejecutar `php artisan migrate --force`, `php artisan optimize` y `php artisan test`.
