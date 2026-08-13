<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi\DTOs;

use JsonSerializable;

class EstadoCfdiResponseDto implements JsonSerializable
{
    public function __construct(
        public bool $ok,
        public string $id,
        public string $status,
        public string $message,
        public string $cancelabilidad,
        public string $cancelacion,
        public array $flags,
        public array $raw,
    ) {}

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
