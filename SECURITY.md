# Seguridad

## Versiones soportadas

| Versión | Soporte |
|---------|---------|
| 0.x | Correcciones de seguridad según disponibilidad |

## Reportar vulnerabilidades

Reporta vulnerabilidades por el canal privado del repositorio o contacta al mantenedor antes de abrir un issue público.

Incluye:

- descripción del riesgo
- pasos para reproducirlo
- impacto esperado
- versión afectada

## Consideraciones

- No expongas las rutas HTTP sin autenticación.
- No desactives `SAT_ESTADO_HTTP_VERIFY` salvo debugging controlado.
- Evita compartir XML reales con RFC, UUID, importes o sellos.
