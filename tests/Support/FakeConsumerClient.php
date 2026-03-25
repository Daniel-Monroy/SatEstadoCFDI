<?php

namespace DanielMonroy\SatEstadoCfdi\Tests\Support;

use PhpCfdi\SatEstadoCfdi\Contracts\ConsumerClientInterface;
use PhpCfdi\SatEstadoCfdi\Contracts\ConsumerClientResponseInterface;
use PhpCfdi\SatEstadoCfdi\Utils\ConsumerClientResponse;

final class FakeConsumerClient implements ConsumerClientInterface
{
    private int $consumeCalls = 0;

    /** @param array<string, string> $values */
    public function __construct(
        private readonly array $values,
    ) {}

    public function consume(string $uri, string $expression): ConsumerClientResponseInterface
    {
        $this->consumeCalls++;

        return ConsumerClientResponse::createFromValues($this->values);
    }

    public function consumeCalls(): int
    {
        return $this->consumeCalls;
    }
}
