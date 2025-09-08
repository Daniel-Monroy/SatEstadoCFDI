<?php

namespace DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiHttpResponseDto;
use Illuminate\Support\Facades\Cache;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\SatEstadoCfdi\CfdiStatus;
use PhpCfdi\SatEstadoCfdi\Consumer;

readonly class SatEstadoCfdiService
{
    public function __construct(private Consumer $consumer)
    {
    }

    public function consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null): EstadoCfdiHttpResponseDto
    {
        $doc = new \DOMDocument();
        $doc->load($xmlPath);
        $expression = (new DiscoverExtractor())->extract($doc);
        return $this->consultByExpression($expression, $ttlSeconds);
    }

    public function consultByExpression(string $expression, ?int $ttlSeconds = null): EstadoCfdiHttpResponseDto
    {
        $ttl = $ttlSeconds ?? (int)config('sat-estado-cfdi.cache_ttl', 900);

        parse_str($expression, $params);
        $id = $params['id'] ?? null;

        return Cache::remember('sat_estado:' . md5($expression), $ttl, function () use ($expression, $id) {
            return new EstadoCfdiHttpResponseDto(
                $this->consumer->execute($expression),
                $expression,
                $id,
            );
        });
    }
}
