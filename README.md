# Henrik

Henrik es el inventario modular de la Oficina de Informática de la Municipalidad de Doñihue. Controla equipos, repuestos, insumos, ubicaciones, stock, movimientos y responsables de cada cambio.

## Versión funcional actual

- Catálogo de insumos, repuestos y equipos.
- Tipos de inventario administrables desde la interfaz.
- Registro individual de dispositivos mediante etiqueta o número de serie.
- Áreas compatibles para cada artículo.
- Ubicaciones jerárquicas y mapa real configurable mediante pin o enlace externo.
- Recepción de stock separada del registro de entregas y traslados.
- Artículos asignados a ubicaciones previamente creadas.
- Recepciones editables y eliminables con corrección transaccional del stock.
- Creación rápida de artículos y categorías desde la recepción.
- Entregas, traslados, instalaciones y retiros.
- Alertas de stock mínimo.
- Historial por artículo y por equipo.
- Auditoría de creaciones, modificaciones y bajas explicada en lenguaje simple.
- Identidad delegada desde la plataforma principal mediante correo Microsoft.
- Tema claro y oscuro con una interfaz azul oscuro.

La gestión de permisos permanece en la plataforma principal. Henrik valida la identidad firmada que recibe y no mantiene un panel de usuarios o roles propio.

## Requisitos técnicos

- PHP 8.3 o superior.
- Composer 2.
- Laravel 13.
- SQLite para desarrollo; MySQL o PostgreSQL recomendado en producción.
- Extensiones PHP habituales de Laravel: PDO, Mbstring, OpenSSL, Tokenizer, XML y Ctype.

## Instalación local

```bash
composer install
cp .env.example .env
touch database/database.sqlite
php artisan key:generate
```

Antes de iniciar, abre `.env` y configura la identidad local:

```dotenv
HENRIK_ALLOW_LOCAL_IDENTITY=true
HENRIK_LOCAL_USER_EMAIL=tu-correo@donihue.cl
HENRIK_LOCAL_USER_NAME="Tu nombre"
```

Después ejecuta:

```bash
php artisan migrate --seed
php artisan optimize:clear
php artisan serve
```

Abre `http://127.0.0.1:8000`. Si el puerto está ocupado, usa `php artisan serve --port=8001` y abre `http://127.0.0.1:8001`.

### Error Unauthorized en desarrollo

Si aparece **Unauthorized**, revisa que `.env` tenga `APP_ENV=local` y `HENRIK_ALLOW_LOCAL_IDENTITY=true`. Después ejecuta:

```bash
php artisan optimize:clear
```

La identidad local solo funciona con `APP_ENV=local` o `testing`. En producción, Henrik exige la identidad firmada que entrega la plataforma principal.

## Pruebas y formato

```bash
php artisan test
vendor/bin/pint --test
```

La suite comprueba autenticación delegada, auditoría comprensible, asignación por ubicación, creación rápida, edición y eliminación de recepciones, alertas, entregas sin stock, traslados atómicos, bajas lógicas y equipos serializados.

## Documentación

- [Alcance y requisitos](docs/REQUISITOS.md)
- [Arquitectura y modelo](docs/ARQUITECTURA.md)
- [Despliegue](docs/DESPLIEGUE.md)

## Versionado

El código usa versionado semántico. La versión actual es `0.3.0`. La auditoría de datos es independiente de las versiones del software. Cuando se publique una API externa, sus rutas comenzarán con `/api/v1` para permitir cambios compatibles en el futuro.
