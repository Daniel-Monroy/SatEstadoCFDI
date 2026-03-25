# Errores y respuestas

## `404 not_found`

Se devuelve cuando el SAT responde que el CFDI no fue encontrado.

Ejemplo:

```json
{
  "ok": false,
  "status": "not_found",
  "message": "El CFDI no fue encontrado.",
  "data": {
    "query": {
      "isFound": false
    }
  }
}
```

## `422 invalid_xml`

Se devuelve cuando el archivo XML:

- no puede leerse
- está mal formado
- no puede procesarse como CFDI válido

Ejemplo:

```json
{
  "ok": false,
  "status": "invalid_xml",
  "message": "El XML proporcionado es invalido."
}
```

## `422` por validación de expresión

Se devuelve cuando la expresión no incluye al menos:

- `id`
- `re`
- `rr`
- `tt`

Ejemplo de mensaje:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "expression": [
      "La expresion debe incluir los parametros requeridos: rr, tt."
    ]
  }
}
```

## Errores del SAT o red

El paquete aplica timeouts y reintentos configurables, pero sigue dependiendo del servicio externo del SAT.

Recomendaciones:

- usar caché
- revisar timeouts
- no asumir disponibilidad total del SAT
