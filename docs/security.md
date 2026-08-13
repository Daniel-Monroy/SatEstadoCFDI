# Seguridad

## Carga de XML

El paquete endurece la lectura de XML usando `LIBXML_NONET` para evitar que el parser intente resolver recursos de red
durante la carga del documento.

Además:

- valida que el archivo exista y sea legible
- limita el tamaño del XML con `SAT_ESTADO_MAX_XML_KB`
- corta el flujo cuando el XML es inválido
- responde `422` en la capa HTTP opcional en lugar de generar un `500`

## Expresiones impresas

La expresión del SAT se normaliza y valida antes de consultarse. Para la integración HTTP se requiere que incluya al
menos:

- `id`
- `re`
- `rr`
- `tt`

## Dependencias

Se añadieron restricciones en Composer para bloquear rangos vulnerables de:

- `league/commonmark`
- `symfony/http-foundation`
- `symfony/process`

También se recomienda ejecutar periódicamente:

```bash
composer audit
```

## Reintentos y timeouts

La integración HTTP usa timeouts y reintentos configurables. Esto ayuda frente a intermitencias del SAT, pero no debe
considerarse sustituto de observabilidad o manejo de errores en tu aplicación.

## Recomendaciones de uso

- protege los endpoints HTTP con middleware apropiado
- evita exponerlos públicamente sin autenticación
- usa caché para reducir dependencia del SAT
- no uses `SAT_ESTADO_HTTP_VERIFY=false` salvo debugging controlado
- registra errores y latencia si la consulta es crítica para tu flujo
