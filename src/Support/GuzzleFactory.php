<?php

namespace DanielMonroy\SatEstadoCfdi\Support;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzlePsr18;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\StreamFactoryInterface;

final class GuzzleFactory
{
    /**
     * @return array{0:ClientInterface,1:RequestFactoryInterface&StreamFactoryInterface}
     */
    public static function makeFromConfig(array $config): array
    {
        $http = $config['http'] ?? [];

        $stack = HandlerStack::create();

        // Retry middleware (exponencial + jitter)
        $retries = (int)($http['retries'] ?? 3);
        $retryStatuses = $http['retry_statuses'] ?? [500, 502, 503, 504];
        $retryMethods = array_map('strtoupper', $http['retry_methods'] ?? ['GET', 'POST']);
        $baseMs = (int)($http['retry_base_ms'] ?? 500);
        $jitterMax = (int)($http['retry_jitter_max'] ?? 250);

        $decider = function (
            int              $retriesCount,
            RequestInterface $request,
                             $response = null,
                             $exception = null
        ) use ($retries, $retryStatuses, $retryMethods): bool {
            if ($retriesCount >= $retries) {
                return false;
            }
            if (!in_array(strtoupper($request->getMethod()), $retryMethods, true)) {
                return false;
            }
            if ($exception) {
                return true; // fallos de red / timeouts
            }
            if ($response && in_array($response->getStatusCode(), $retryStatuses, true)) {
                return true;
            }
            return false;
        };

        $delay = function (int $retriesCount) use ($baseMs, $jitterMax): int {
            //  base * 2^n + jitter [0..jitterMax]
            return (int)($baseMs * (2 ** $retriesCount)) + random_int(0, $jitterMax);
        };

        $stack->push(Middleware::retry($decider, $delay));

        $guzzle = new GuzzleClient([
            'handler' => $stack,
            'timeout' => (float)($http['timeout'] ?? 10),
            'connect_timeout' => (float)($http['connect_timeout'] ?? 5),
            'verify' => $http['verify'] ?? true,
            'proxy' => $http['proxy'] ?? null,
        ]);

        if (!empty($http['ca_bundle'])) {
            $guzzle = new GuzzleClient(array_merge($guzzle->getConfig(), [
                'verify' => $http['ca_bundle'],
            ]));
        }

        $psr18 = new GuzzlePsr18($guzzle);
        $psr17 = new Psr17Factory();

        return [$psr18, $psr17];
    }
}
