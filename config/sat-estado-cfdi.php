<?php

return [
    // Rutas HTTP opcionales (controller incluido en el paquete)
    'expose_routes' => env('SAT_ESTADO_EXPOSE_ROUTES', false),
    'route_prefix'  => env('SAT_ESTADO_ROUTE_PREFIX', 'api'),
    'middleware'    => ['api', 'auth:sanctum'],

    // HTTP Client (Guzzle) — timeouts y reintentos
    'http' => [
        'timeout'          => env('SAT_ESTADO_HTTP_TIMEOUT', 10),   // seg
        'connect_timeout'  => env('SAT_ESTADO_CONNECT_TIMEOUT', 5),
        'retries'          => env('SAT_ESTADO_RETRIES', 3),
        'retry_statuses'   => [500, 502, 503, 504],
        'retry_methods'    => ['GET', 'POST'],
        'retry_base_ms'    => env('SAT_ESTADO_RETRY_BASE_MS', 500), // base backoff
        'retry_jitter_max' => env('SAT_ESTADO_RETRY_JITTER_MAX', 250),
        // Proxy / verify / certs si lo necesitas:
        'proxy'            => env('SAT_ESTADO_HTTP_PROXY'),  // ej: http://user:pass@host:port
        'verify'           => env('SAT_ESTADO_HTTP_VERIFY', true), // false para desactivar verificación TLS
        'ca_bundle'        => env('SAT_ESTADO_CA_BUNDLE'), // ruta a cacert.pem si aplica
    ],

    // Cache TTL (segundos) por expresión QR
    'cache_ttl' => env('SAT_ESTADO_CACHE_TTL', 900), // 15 min
];
