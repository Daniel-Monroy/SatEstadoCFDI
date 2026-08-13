# Guía de actualización

## De 0.1 a 0.2

### Requisitos

- PHP mínimo: 8.3
- Laravel soportado: 12 o 13

### Cambios

- Se elimina compatibilidad declarada con Laravel 11.
- Se agrega `SAT_ESTADO_MAX_XML_KB` para limitar uploads XML.
- Se mantienen métodos públicos del servicio y facade.
- Se mantiene estructura JSON de respuestas.

### Pasos

1. Actualiza dependencias con `composer update daniel-monroy/sat-estado-cfdi --with-all-dependencies`.
2. Si publicaste configuración, agrega `max_xml_kb` en `config/sat-estado-cfdi.php`.
3. Ejecuta tu suite de pruebas.
