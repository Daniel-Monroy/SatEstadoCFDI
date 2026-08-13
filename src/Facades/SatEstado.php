<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi\Facades;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiNotFoundDto;
use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiResponseDto;
use Illuminate\Support\Facades\Facade;

/**
 * @method static EstadoCfdiResponseDto|EstadoCfdiNotFoundDto consultFromXmlPath(string $xmlPath, ?int $ttlSeconds = null)
 * @method static EstadoCfdiResponseDto|EstadoCfdiNotFoundDto consultByExpression(string $expression, ?int $ttlSeconds = null)
 */
class SatEstado extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sat-estado-cfdi';
    }
}
