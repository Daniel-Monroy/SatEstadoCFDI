# Uso

## Resolver el servicio desde el contenedor

```php
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;

$response = app(SatEstadoCfdiService::class)->consultByExpression(
    'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000&fe=ABCD1234'
);
```

## Usar el facade

```php
use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;

$response = SatEstado::consultByExpression(
    'id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000'
);
```

## Consultar desde XML

```php
use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;

$response = SatEstado::consultFromXmlPath(storage_path('app/cfdi.xml'));
```

## Respuesta esperada

La respuesta normalizada del paquete expone:

- `ok`
- `id`
- `status`
- `message`
- `cancelabilidad`
- `cancelacion`
- `flags`
- `raw`

Ejemplo:

```php
if ($response->ok && $response->status === 'active') {
    // CFDI vigente
}
```

## Expresiones soportadas

El paquete acepta:

- La URL completa de verificación del SAT
- Solo el query string
- Una expresión sin `?` inicial

Ejemplos válidos:

```text
https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000&fe=ABCD1234
```

```text
id=UUID&re=AAA010101AAA&rr=BBB010101BBB&tt=123.450000&fe=ABCD1234
```

## Caché

Las respuestas se almacenan por expresión usando el TTL definido en `sat-estado-cfdi.cache_ttl`.

Puedes sobreescribirlo por llamada:

```php
$response = SatEstado::consultByExpression($expression, 60);
```
