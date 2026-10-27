# Alcance y requisitos

## Objetivo

Centralizar el inventario tecnológico de la Oficina de Informática. La primera ubicación es la Municipalidad de Doñihue, pero el sistema permite crear libremente nuevas sedes, edificios, pisos, oficinas, bodegas y zonas.

## Alcance de la versión 0.1.0

| Necesidad | Implementación |
|---|---|
| Definir áreas compatibles | Cada artículo se asocia a una o más áreas de servicio. |
| Ubicar insumos y equipos | Stock por ubicación, equipos serializados y mapa esquemático. |
| Historial | Movimientos por artículo/equipo y auditoría de cambios. |
| Tema claro y oscuro | Preferencia guardada en el navegador. |
| Identidad y accesos | Correo Microsoft recibido y firmado por la plataforma principal. Los permisos se administran fuera de Henrik. |
| Aviso de stock | Alerta cuando una existencia llega a su mínimo configurado. |
| Crear, modificar y eliminar | CRUD con baja lógica para conservar trazabilidad. |
| Distribuir stock | La versión inicial muestra stock, mínimos y ubicaciones. La priorización automática se planifica para una versión posterior. |
| Tickets | Fuera del alcance inicial; queda preparado como módulo futuro. |

## Reglas principales

1. Un artículo puede controlarse por cantidad o individualmente.
2. Una salida nunca puede dejar stock negativo.
3. Un traslado descuenta origen y suma destino en una sola transacción.
4. Los artículos y ubicaciones se dan de baja lógicamente; su historial permanece.
5. Todo cambio incluye correo, nombre e identificador externo del responsable cuando están disponibles.
6. Una ubicación con stock o equipos no puede darse de baja hasta ser vaciada.

## Siguiente versión

- Solicitudes y tickets de usuarios.
- Carga de planos reales como fondo del mapa.
- Recomendación de distribución según faltantes y equipos compatibles.
- Importación y exportación masiva.
- API pública versionada y conectores con otras plataformas.
- Notificaciones por correo o Teams.
