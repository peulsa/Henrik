# Alcance y requisitos

## Objetivo

Centralizar el inventario tecnológico de la Oficina de Informática. La primera ubicación es la Municipalidad de Doñihue, pero el sistema permite crear libremente nuevas sedes, edificios, pisos, oficinas, bodegas y zonas.

## Alcance de la versión 0.3.0

| Necesidad | Implementación |
|---|---|
| Definir áreas compatibles | Cada artículo se asocia a una o más áreas de servicio. |
| Ubicar insumos y equipos | Cada artículo se asigna a una ubicación creada; incluye stock, dispositivos individuales y mapa real con pin o enlace externo. |
| Historial | Movimientos por artículo/equipo y auditoría de cambios redactada para usuarios no técnicos. |
| Tema claro y oscuro | Preferencia guardada en el navegador. |
| Identidad y accesos | Correo Microsoft recibido y firmado por la plataforma principal. Los permisos se administran fuera de Henrik. |
| Aviso de stock | Alerta cuando una existencia llega a su mínimo configurado. |
| Crear, modificar y eliminar | CRUD de artículos y tipos configurables; baja lógica para conservar trazabilidad. |
| Distribuir stock | La versión inicial muestra stock, mínimos y ubicaciones. La priorización automática se planifica para una versión posterior. |
| Tickets | Fuera del alcance inicial; queda preparado como módulo futuro. |

## Reglas principales

1. Un artículo puede controlarse por cantidad o individualmente.
2. Una salida nunca puede dejar stock negativo.
3. Un traslado descuenta origen y suma destino en una sola transacción.
4. Los artículos y ubicaciones se dan de baja lógicamente; su historial permanece.
5. Todo cambio incluye correo, nombre e identificador externo del responsable cuando están disponibles.
6. Una ubicación con stock o equipos no puede darse de baja hasta ser vaciada.
7. La recepción invita a seleccionar un artículo existente; si no existe, permite crear una ficha breve sin salir del formulario.
8. Una recepción puede editarse o eliminarse solamente si el stock disponible permite revertirla sin dejar cantidades negativas.

## Siguiente versión

- Solicitudes y tickets de usuarios.
- Carga de planos reales como fondo del mapa.
- Recomendación de distribución según faltantes y equipos compatibles.
- Importación y exportación masiva.
- API pública versionada y conectores con otras plataformas.
- Notificaciones por correo o Teams.
