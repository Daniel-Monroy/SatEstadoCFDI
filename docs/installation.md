# Instalación

## Requisitos

- PHP 8.3 o 8.4
- Laravel 12 o 13
- Extensión `ext-dom`

## Instalación con Composer

```bash
composer require daniel-monroy/sat-estado-cfdi
```

## Publicar configuración

```bash
php artisan vendor:publish --tag="sat-estado-cfdi-config"
```

Esto publicará el archivo `config/sat-estado-cfdi.php`.

## Configuración mínima

```php
return [
    'expose_routes' => env('SAT_ESTADO_EXPOSE_ROUTES', false),
    'route_prefix' => env('SAT_ESTADO_ROUTE_PREFIX', 'api'),
    'middleware' => env('SAT_ESTADO_ROUTE_MIDDLEWARE', 'api'),
    'max_xml_kb' => env('SAT_ESTADO_MAX_XML_KB', 2048),
    'cache_ttl' => env('SAT_ESTADO_CACHE_TTL', 900),
];
```

## Variables de entorno sugeridas

```env
SAT_ESTADO_EXPOSE_ROUTES=false
SAT_ESTADO_ROUTE_PREFIX=api
SAT_ESTADO_ROUTE_MIDDLEWARE=api,auth:sanctum
SAT_ESTADO_MAX_XML_KB=2048
SAT_ESTADO_CACHE_TTL=900
SAT_ESTADO_HTTP_TIMEOUT=10
SAT_ESTADO_CONNECT_TIMEOUT=5
SAT_ESTADO_RETRIES=3
```

## Siguiente paso

- Si quieres usar el paquete solo desde código, ve a [usage.md](./usage.md).
- Si quieres exponer endpoints HTTP, ve a [http-integration.md](./http-integration.md).
