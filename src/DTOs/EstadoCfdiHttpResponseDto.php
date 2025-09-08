<?php

namespace DanielMonroy\SatEstadoCfdi\DTOs;

use PhpCfdi\SatEstadoCfdi\CfdiStatus;

class EstadoCfdiHttpResponseDto
{
    public function __construct(
        public CfdiStatus $status,
        public string     $expression,
        public ?string    $id = null,
    )
    {
    }
}
