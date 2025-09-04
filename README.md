# SatEstadoCFDI (Laravel Package)

Este paquete integra la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi) a **Laravel 11 /
12**
para consultar el **estado de un CFDI** directamente en el **servicio web del SAT**.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/daniel-monroy/sat-estado-cfdi.svg?style=flat-square)](https://packagist.org/packages/daniel-monroy/sat-estado-cfdi)
[![Total Downloads](https://img.shields.io/packagist/dt/daniel-monroy/sat-estado-cfdi.svg?style=flat-square)](https://packagist.org/packages/daniel-monroy/sat-estado-cfdi)

El protocolo que utiliza el SAT para esta consulta es **SOAP sobre HTTP/HTTPS**, expuesto como **servicio web**.  
Este paquete se conecta mediante el **cliente HTTP (PSR-18)**, con compatibilidad para middlewares (reintentos,
timeouts, logging, etc.).

## 📦 Instalación

Instálalo vía Composer:

```bash
composer require daniel-monroy/sat-estado-cfdi
```

## ⚙️ Configuración

Publica el archivo de configuración:

```bash
php artisan vendor:publish --tag="sat-estado-cfdi-config"
```

Ejemplo de configuración en `config/sat-estado-cfdi.php`:

```php
return [
    'expose_routes' => env('SAT_ESTADO_EXPOSE_ROUTES', false),
    'route_prefix'  => env('SAT_ESTADO_ROUTE_PREFIX', 'api'),
    'middleware'    => ['api', 'auth:sanctum'],
    'cache_ttl'     => env('SAT_ESTADO_CACHE_TTL', 900), // segundos
];
```

En el archivo `.env` puedes definir:

```env
SAT_ESTADO_EXPOSE_ROUTES=true
SAT_ESTADO_ROUTE_PREFIX=api
SAT_ESTADO_CACHE_TTL=900
```

## 📡 Endpoints expuestos (opcional)

Si `SAT_ESTADO_EXPOSE_ROUTES` está en `true`, se habilitan los siguientes endpoints:

| Método | Endpoint                        | Descripción                          |
|--------|---------------------------------|--------------------------------------|
| POST   | `/api/sat-estado-cfdi/consulta` | Consulta el estado de un CFDI        |
| GET    | `/api/sat-estado-cfdi/status`   | Verifica que el servicio esté activo |

El prefijo (`/api/sat-estado-cfdi`) y el middleware (ej. `auth`, `sanctum`, etc.) pueden modificarse con la variable
`SAT_ESTADO_ROUTE_PREFIX`.

### Ejemplo de consulta

```bash
curl -X POST http://tu-dominio.test/api/sat-estado-cfdi/consulta \
-H "Content-Type: application/json" \
-d '{
  "expression": "id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=1234.56"
}'
```

o con multipart/form-data enviando directamente el archivo `XML`:

```bash
curl -X POST http://tu-dominio.test/api/sat-estado-cfdi/
consulta \
-H "Content-Type: multipart/form-data" \
-F "xml=@/ruta/al/archivo.xml"
```

La respuesta será similar a:

```json
{
  "ok": true,
  "estatus": "activo",
  "message": "El CFDI es válido y está activo.",
  "cancelabilidad": "sin_aceptacion",
  "cancelacion": "no_cancelado",
  "flags": {
    "isActive": true,
    "isCancelled": false,
    "isPendingCancel": false
  },
  "raw": {
    "query": {
      "isFound": true
    },
    "document": {
      "isActive": true,
      "isCancelled": false
    },
    "cancellable": {
      "isCancellableByDirect": true,
      "isCancellableByApproval": false
    },
    "cancellation": {
      "isCancelledByDirect": false,
      "isCancelledByApproval": false,
      "isCancelledByExpiration": false,
      "isPending": false,
      "isDisapproved": false,
      "isUndefined": false
    },
    "efos": "Excluded"
  }
}
```

🧪 Ejemplo de uso en código

```php
use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;

$expresion = '?re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000&id=UUID...';
$status = SatEstado::consultByExpression($expresion);
if ($status->document->isActive()) {
    echo "El CFDI está activo";
}
```

📌 Notas
- El paquete se apoya en y utiliza la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi)
- El servicio del SAT puede ser intermitente, se recomienda configurar caché y reintentos.
- Los estados dependen de la respuesta oficial del SAT.

📄 Licencia
Este paquete es software libre bajo la licencia MIT.
