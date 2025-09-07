<?php

namespace DanielMonroy\SatEstadoCfdi\DTOs;

class EstadoCfdiNotFoundDto implements \JsonSerializable
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
                ]
            ],
        ];
    }
}