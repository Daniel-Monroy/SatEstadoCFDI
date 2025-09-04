<?php

namespace DanielMonroy\SatEstadoCfdi\Services;

use Illuminate\Support\Facades\Cache;
use PhpCfdi\CfdiExpresiones\DiscoverExtractor;
use PhpCfdi\SatEstadoCfdi\Consumer;

readonly class SatEstadoCfdiService
{
    public function __construct(private Consumer $consumer)
    {
    }

    public function consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null)
    {
        $doc = new \DOMDocument();
        $doc->load($xmlPath);

        $expression = (new DiscoverExtractor())->extract($doc);

        return $this->consultByExpression($expression, $ttlSeconds);
    }

    public function consultByExpression(string $expression, ?int $ttlSeconds = null)
    {
        $ttl = $ttlSeconds ?? (int)config('sat-estado-cfdi.cache_ttl', 900);

        return Cache::remember('sat_estado:' . md5($expression), $ttl, function () use ($expression) {
            return $this->consumer->execute($expression);
        });
    }
}
