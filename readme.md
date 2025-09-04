# SatEstadoCFDI (Laravel Package)

Este paquete integra la librería [phpcfdi/sat-estado-cfdi](https://github.com/phpcfdi/sat-estado-cfdi) a **Laravel 11 /
12**
para consultar el **estado de un CFDI** directamente en el **servicio web del SAT**.

El protocolo que utiliza el SAT para esta consulta es **SOAP sobre HTTP/HTTPS**, expuesto como **servicio web**.  
Este paquete se conecta mediante el **cliente HTTP (PSR-18)**, con compatibilidad para middlewares (reintentos,
timeouts, logging, etc.).
