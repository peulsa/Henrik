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
- SQLite para desarrollo; MySQL o PostgreSQL recomendado en producción.
- Extensiones PHP habituales de Laravel: PDO, Mbstring, OpenSSL, Tokenizer, XML y Ctype.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Para trabajar localmente, configura en `.env`:

```dotenv
HENRIK_ALLOW_LOCAL_IDENTITY=true
HENRIK_LOCAL_USER_EMAIL=tu-correo@donihue.cl
HENRIK_LOCAL_USER_NAME="Tu nombre"
```

Esta excepción solo funciona con `APP_ENV=local` o `testing`. En producción, Henrik exige la firma de la plataforma principal.

## Pruebas y formato

```bash
php artisan test
vendor/bin/pint --test
```

La suite comprueba autenticación delegada, auditoría comprensible, asignación por ubicación, creación rápida, edición y eliminación de recepciones, alertas, entregas sin stock, traslados atómicos, bajas lógicas y equipos serializados.

## Documentación

- [Alcance y requisitos](docs/REQUISITOS.md)
- [Arquitectura y modelo](docs/ARQUITECTURA.md)
- [Integración con la plataforma](docs/INTEGRACION.md)
- [Despliegue](docs/DESPLIEGUE.md)
- [Registro de versiones](CHANGELOG.md)

## Versionado

El código usa versionado semántico. La versión actual es `0.3.0`. La auditoría de datos es independiente de las versiones del software. Cuando se publique una API externa, sus rutas comenzarán con `/api/v1` para permitir cambios compatibles en el futuro.
