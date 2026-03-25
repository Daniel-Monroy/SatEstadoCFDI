<?php

namespace DanielMonroy\SatEstadoCfdi\Tests\Support;

use PhpCfdi\SatEstadoCfdi\Contracts\ConsumerClientInterface;
use PhpCfdi\SatEstadoCfdi\Contracts\ConsumerClientResponseInterface;
use PhpCfdi\SatEstadoCfdi\Utils\ConsumerClientResponse;

final class FakeConsumerClient implements ConsumerClientInterface
{
    /** @param array<string, string> $values */
    public function __construct(
        private readonly array $values,
    ) {}

    public function consume(string $uri, string $expression): ConsumerClientResponseInterface
    {
        return ConsumerClientResponse::createFromValues($this->values);
    }
}
