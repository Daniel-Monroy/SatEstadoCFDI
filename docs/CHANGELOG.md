# SatEstadoCFDI — Changelog

## [v0.1.0] - 2026-03-24

- Compatibilidad declarada y validada con Laravel 13.
- Compatibilidad operativa revisada para PHP 8.4.
- Endurecimiento de la carga XML con `LIBXML_NONET` y manejo controlado de errores.
- Validación de expresiones impresas del SAT con campos requeridos.
- Soporte correcto para expresiones completas en formato URL del SAT.
- Mejora de consistencia y legibilidad en provider, DTOs, facade y servicios.
- Agregada suite inicial con Pest y Orchestra Testbench.
- Agregadas herramientas de desarrollo para pruebas y formateo.
- Documentación ampliada en `docs/`.

## Versión 0.0.x

- 🚀 **Versión inicial del paquete.**
- 🔗 Integración con la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi).
- ⚙️ Configuración del cliente HTTP (PSR-18) para consumir el servicio SOAP del SAT.
- 📖 Inclusión de documentación inicial en el archivo `README.md`.
- 🧪 Pruebas unitarias básicas para validar la funcionalidad principal.
- 📦 Publicación del paquete para su instalación vía Composer.
- 🎯 Compatibilidad confirmada con **Laravel 11** y **Laravel 12**.
