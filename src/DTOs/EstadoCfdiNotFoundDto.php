<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi\DTOs;

use JsonSerializable;

class EstadoCfdiNotFoundDto implements JsonSerializable
{
    public function jsonSerialize(): array
    {
        return [
            'ok' => false,
            'status' => 'not_found',
            'message' => 'El CFDI no fue encontrado.',
            'data' => [
                'query' => [
                    'isFound' => false,
                ],
            ],
        ];
    }
}
