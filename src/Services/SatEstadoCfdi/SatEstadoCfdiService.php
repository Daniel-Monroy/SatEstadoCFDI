<?php

namespace DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiHttpResponseDto;
use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiNotFoundDto;
use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiResponseDto;
use DanielMonroy\SatEstadoCfdi\Support\PrintedExpression;
use DOMDocument;
use Illuminate\Support\Facades\Cache;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\SatEstadoCfdi\Consumer;
use RuntimeException;

readonly class SatEstadoCfdiService
{
    public function __construct(
        private Consumer $consumer,
        private EstadoCfdiResponseNormalizerService $normalizer
    ) {}

    /**
     * Consulta el estado de un CFDI a partir de la ruta de un archivo XML.
     *
     * Este método carga un archivo XML desde la ruta proporcionada, extrae la expresión
     * necesaria para la consulta utilizando `DiscoverExtractor`, y delega la consulta
     * al método `consultByExpression`.
     *
     * @param  string  $xmlPath  La ruta del archivo XML que contiene el CFDI.
     * @param  int|null  $ttlSeconds  (Opcional) Tiempo de vida en segundos para el almacenamiento en caché.
     *                                Si no se proporciona, se utiliza el valor predeterminado configurado.
     * @return EstadoCfdiResponseDto|EstadoCfdiNotFoundDto Devuelve un DTO con la respuesta del estado del CFDI
     *                                                     o un DTO indicando que no se encontró.
     */
    public function consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null): EstadoCfdiResponseDto|EstadoCfdiNotFoundDto
    {
        if (! is_file($xmlPath) || ! is_readable($xmlPath)) {
            throw new RuntimeException('No fue posible leer el archivo XML proporcionado.');
        }

        $previousUseInternalErrors = libxml_use_internal_errors(true);
        libxml_clear_errors();

        $doc = new DOMDocument;
        $loaded = $doc->load($xmlPath, LIBXML_NONET);

        libxml_clear_errors();
        libxml_use_internal_errors($previousUseInternalErrors);

        if (! $loaded) {
            throw new RuntimeException('El XML proporcionado es invalido.');
        }

        $expression = (new DiscoverExtractor)->extract($doc);

        return $this->consultByExpression($expression, $ttlSeconds);
    }

    /**
     * Consulta el estado de un CFDI a partir de una expresión proporcionada.
     *
     * Este método utiliza una expresión para consultar el estado de un CFDI. Si se proporciona un tiempo
     * de vida en segundos (`ttlSeconds`), se utiliza para almacenar en caché la respuesta; de lo contrario,
     * se utiliza un valor predeterminado configurado. La respuesta se almacena en caché para optimizar
     * el rendimiento de consultas repetidas.
     *
     * @param  string  $expression  La expresión que contiene los parámetros necesarios para la consulta.
     * @param  int|null  $ttlSeconds  (Opcional) Tiempo de vida en segundos para el almacenamiento en caché.
     *                                Si no se proporciona, se utiliza el valor predeterminado configurado.
     * @return EstadoCfdiResponseDto|EstadoCfdiNotFoundDto Devuelve un DTO con la respuesta del estado del CFDI
     *                                                     o un DTO indicando que no se encontró.
     */
    public function consultByExpression(string $expression, ?int $ttlSeconds = null): EstadoCfdiResponseDto|EstadoCfdiNotFoundDto
    {
        $ttl = $ttlSeconds ?? (int) config('sat-estado-cfdi.cache_ttl', 900);

        $params = PrintedExpression::parse($expression);
        $id = $params['id'] ?? 'unknown';

        $cfdiResponse = Cache::remember(
            'sat_estado:'.hash('sha256', $expression),
            $ttl,
            fn () => new EstadoCfdiHttpResponseDto(
                $this->consumer->execute($expression),
                $expression,
                $id
            )
        );

        if (! $cfdiResponse->status->query->isFound()) {
            return new EstadoCfdiNotFoundDto;
        }

        return $this->normalizer->toDto($cfdiResponse->status, $cfdiResponse->id ?? $id);
    }
}
