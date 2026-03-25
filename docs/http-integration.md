# Integración HTTP

La capa HTTP es opcional. El valor principal del paquete está en el servicio y el facade; los endpoints incluidos son
una conveniencia para proyectos Laravel que quieran exponer esta consulta rápidamente.

## Activación

```env
SAT_ESTADO_EXPOSE_ROUTES=true
SAT_ESTADO_ROUTE_PREFIX=api
SAT_ESTADO_ROUTE_MIDDLEWARE=api,auth:sanctum
```

## Endpoints

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `POST` | `/api/cfdi/estado` | Consulta el estado de un CFDI |
| `GET` | `/api/cfdi/estatus` | Verifica disponibilidad del paquete |

## Consulta por expresión

```bash
curl -X POST http://tu-dominio.test/api/cfdi/estado \
  -H "Content-Type: application/json" \
  -d '{
    "expression": "id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45"
  }'
```

## Consulta por XML

```bash
curl -X POST http://tu-dominio.test/api/cfdi/estado \
  -H "Content-Type: multipart/form-data" \
  -F "xml=@/ruta/al/archivo.xml"
```

## Respuesta de salud

```json
{
  "ok": true,
  "status": "available",
  "message": "El servicio del paquete está disponible."
}
```

## Códigos de estado esperados

- `200`: consulta exitosa
- `404`: CFDI no encontrado
- `422`: XML inválido o expresión incompleta

## Middleware

La configuración `SAT_ESTADO_ROUTE_MIDDLEWARE` puede recibirse como lista separada por comas:

```env
SAT_ESTADO_ROUTE_MIDDLEWARE=api,auth:sanctum,throttle:api
```
