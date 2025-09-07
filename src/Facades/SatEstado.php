<?php

namespace DanielMonroy\SatEstadoCfdi\Facades;

use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;

/**
 * @method static mixed consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null)
 * @method static mixed consultByExpression(string $expression, ?int $ttlSeconds = null)
 */
class SatEstado
{
    protected static function getFacadeAccessor(): string
    {
        return SatEstadoCfdiService::class;
    }
}
