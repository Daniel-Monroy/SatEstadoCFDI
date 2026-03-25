<?php

namespace DanielMonroy\SatEstadoCfdi\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null)
 * @method static mixed consultByExpression(string $expression, ?int $ttlSeconds = null)
 */
class SatEstado extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sat-estado-cfdi';
    }
}
