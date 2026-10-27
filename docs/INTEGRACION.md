# Integración con la plataforma principal

La plataforma principal autentica al usuario con Microsoft y decide si puede acceder. Henrik recibe esa identidad mediante encabezados firmados. El proxy o backend integrador debe impedir que un cliente externo escriba estos encabezados directamente.

## Encabezados

| Encabezado | Contenido |
|---|---|
| `X-Henrik-User-Email` | Correo Microsoft del usuario. |
| `X-Henrik-User-Name` | Nombre visible. |
| `X-Henrik-User-Id` | Identificador estable de Microsoft Entra o de la plataforma. |
| `X-Henrik-Timestamp` | Unix timestamp del momento de la solicitud. |
| `X-Henrik-Signature` | HMAC SHA-256 del contenido definido abajo. |

La carga firmada es:

```text
timestamp|external_id|email|name
```

La firma usa el secreto `HENRIK_PLATFORM_SHARED_SECRET`. Henrik rechaza firmas distintas y timestamps con más de cinco minutos de diferencia.

```php
$timestamp = (string) time();
$payload = implode('|', [$timestamp, $externalId, $email, $name]);
$signature = hash_hmac('sha256', $payload, $sharedSecret);
```

La plataforma principal administra inicio de sesión, usuarios y permisos. Henrik registra la identidad recibida en movimientos y auditorías. Si después se requieren permisos específicos dentro del inventario, deben enviarse como capacidades firmadas y documentarse como una nueva versión del contrato.
