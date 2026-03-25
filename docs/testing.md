# Testing

## Instalar dependencias de desarrollo

```bash
composer install
```

## Ejecutar pruebas

```bash
composer test
```

o directamente:

```bash
vendor/bin/pest
```

## Ejecutar una suite o archivo específico

```bash
vendor/bin/pest tests/Feature/HttpEndpointsTest.php
```

## Validar formato

```bash
composer lint
```

o:

```bash
vendor/bin/pint --test
```

## Cobertura actual

La suite inicial cubre:

- endpoint de salud
- consulta HTTP por expresión
- respuesta `404` cuando el CFDI no existe
- validación de expresiones incompletas
- respuesta `422` para XML inválido
- uso del facade
- parseo de expresiones impresas del SAT
- consulta desde archivo XML
