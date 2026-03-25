# Arquitectura

## Componentes principales

### `SatEstadoCfdiServiceProvider`

Registra:

- el `Consumer` de `phpcfdi/sat-estado-cfdi`
- el servicio principal del paquete
- el alias usado por el facade
- las rutas HTTP opcionales cuando están habilitadas

### `SatEstadoCfdiService`

Es la puerta de entrada principal de la lógica del paquete.

Responsabilidades:

- consultar por expresión
- consultar desde un archivo XML
- aplicar caché por expresión
- delegar la normalización de la respuesta

### `EstadoCfdiResponseNormalizerService`

Convierte `PhpCfdi\SatEstadoCfdi\CfdiStatus` en un DTO simple y estable para Laravel.

### DTOs

- `EstadoCfdiResponseDto`
- `EstadoCfdiNotFoundDto`
- `EstadoCfdiHttpResponseDto`

Estos encapsulan la respuesta que consume el resto de la aplicación.

### `PrintedExpression`

Helper para:

- extraer el query string de una URL del SAT
- parsear parámetros de una expresión
- validar campos requeridos

### Integración HTTP opcional

Compuesta por:

- `ConsultarEstadoCfdiRequest`
- `ConsultarEstadoCfdiController`
- `routes/api.php`

Esta capa existe como conveniencia. Puede desactivarse por completo si el paquete se usa solo desde código.

## Flujo general

1. Se recibe una expresión o un XML.
2. Si llega XML, se transforma a expresión con `DiscoverExtractor`.
3. Se consulta al SAT con `Consumer`.
4. La respuesta se cachea por expresión.
5. Se normaliza a un DTO del paquete.
6. Opcionalmente, se expone vía JSON en la capa HTTP.
