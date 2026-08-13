# SatEstadoCFDI (Laravel Package)

Este paquete integra la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi) en **Laravel 12 y 13**
para consultar el **estado de un CFDI** directamente en el **servicio web del SAT**.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/daniel-monroy/sat-estado-cfdi.svg?style=flat-square)](https://packagist.org/packages/daniel-monroy/sat-estado-cfdi)
[![Total Downloads](https://img.shields.io/packagist/dt/daniel-monroy/sat-estado-cfdi.svg?style=flat-square)](https://packagist.org/packages/daniel-monroy/sat-estado-cfdi)

El protocolo que utiliza el SAT para esta consulta es **SOAP sobre HTTP/HTTPS**, expuesto como **servicio web**.
Este paquete se conecta mediante un **cliente HTTP PSR-18**, con compatibilidad para reintentos, timeouts, caché y una
integración HTTP opcional para proyectos Laravel.

## Documentación

- [Instalación](./docs/installation.md)
- [Uso](./docs/usage.md)
- [Integración HTTP](./docs/http-integration.md)
- [Errores](./docs/errors.md)
- [Pruebas](./docs/testing.md)
- [Seguridad](./docs/security.md)
- [Arquitectura](./docs/architecture.md)
- [Registro de cambios](./docs/CHANGELOG.md)
- [Contribuir](./CONTRIBUTING.md)
- [Guía de actualización](./UPGRADE.md)

## Compatibilidad

| PHP | Laravel |
|-----|---------|
| 8.3 | 12, 13 |
| 8.4 | 12, 13 |

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
    'middleware'    => env('SAT_ESTADO_ROUTE_MIDDLEWARE', 'api'),
    'max_xml_kb'    => env('SAT_ESTADO_MAX_XML_KB', 2048),
    'cache_ttl'     => env('SAT_ESTADO_CACHE_TTL', 900), // segundos
];
```

En el archivo `.env` puedes definir:

```env
SAT_ESTADO_EXPOSE_ROUTES=true
SAT_ESTADO_ROUTE_PREFIX=api
SAT_ESTADO_ROUTE_MIDDLEWARE=api,auth:sanctum
SAT_ESTADO_MAX_XML_KB=2048
SAT_ESTADO_CACHE_TTL=900
```

## Uso desde Laravel

### Servicio

```php
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;

$response = app(SatEstadoCfdiService::class)->consultByExpression(
    'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000&fe=ABCD1234'
);
```

### Facade

```php
use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;

$response = SatEstado::consultByExpression(
    'id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000'
);
```

### Desde XML

```php
use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;

$response = SatEstado::consultFromXmlPath(storage_path('app/cfdi.xml'));
```

## 📡 Integración HTTP opcional

Si `SAT_ESTADO_EXPOSE_ROUTES` está en `true`, se habilitan los siguientes endpoints:

| Método | Endpoint            | Descripción                          |
|--------|---------------------|--------------------------------------|
| POST   | `/api/cfdi/estado`  | Consulta el estado de un CFDI        |
| GET    | `/api/cfdi/estatus` | Verifica que el servicio esté activo |

El prefijo (`/api`) y el middleware (ej. `auth`, `sanctum`, etc.) pueden modificarse con la variable
`SAT_ESTADO_ROUTE_PREFIX` y `SAT_ESTADO_ROUTE_MIDDLEWARE`.

### Ejemplo de consulta

```bash
curl -X POST http://tu-dominio.test/api/cfdi/estado \
-H "Content-Type: application/json" \
-d '{
  "expression": "id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=1234.56"
}'
```

o con multipart/form-data enviando directamente el archivo `XML`:

```bash
curl -X POST http://tu-dominio.test/api/cfdi/estado \
-H "Content-Type: multipart/form-data" \
-F "xml=@/ruta/al/archivo.xml"
```

La respuesta será similar a:

```json
{
  "ok": true,
  "status": "active",
  "id": "XXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX",
  "message": "El CFDI se encuentra vigente y es válido.",
  "cancelabilidad": "sin_aceptacion",
  "cancelacion": "indefinida",
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
      "isUndefined": true
    },
    "efos": null
  }
}
```

## Manejo de errores

- Si el CFDI no existe, el paquete responde `404` en la integración HTTP opcional y devuelve un DTO `not_found`.
- Si el XML está mal formado o no puede leerse, la integración HTTP opcional responde `422`.
- Si el XML excede `SAT_ESTADO_MAX_XML_KB`, la integración HTTP opcional responde `422`.
- Si la expresión no contiene al menos `id`, `re`, `rr` y `tt`, la validación HTTP responderá `422`.

## Desarrollo

Instala dependencias de desarrollo y ejecuta las herramientas locales:

```bash
composer install
composer test
composer lint
composer analyse
```

## Notas

- El paquete se apoya en y utiliza la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi)
- El servicio del SAT puede ser intermitente, se recomienda configurar caché y reintentos.
- No desactives `SAT_ESTADO_HTTP_VERIFY` salvo debugging controlado.
- Los estados dependen de la respuesta oficial del SAT.
- Si necesitas una integración agnóstica al framework, usa directamente `phpcfdi/sat-estado-cfdi`. Este paquete está
  orientado específicamente a Laravel.

## Licencia

Este paquete usa licencia MIT. Sus dependencias principales usan licencias permisivas compatibles, como MIT, BSD-3-Clause y Apache-2.0.
