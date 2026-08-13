<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\EstadoCfdiResponseNormalizerService;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use DanielMonroy\SatEstadoCfdi\Support\GuzzleFactory;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PhpCfdi\SatEstadoCfdi\Clients\Http\HttpConsumerClient;
use PhpCfdi\SatEstadoCfdi\Clients\Http\HttpConsumerFactory;
use PhpCfdi\SatEstadoCfdi\Consumer;

class SatEstadoCfdiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sat-estado-cfdi.php', 'sat-estado-cfdi');

        // Register the SAT consumer backed by a PSR-18 Guzzle client.
        $this->app->singleton(Consumer::class, function () {
            [$psr18, $psr17] = GuzzleFactory::makeFromConfig(config('sat-estado-cfdi'));

            $factory = new HttpConsumerFactory($psr18, $psr17, $psr17);
            $client = new HttpConsumerClient($factory);

            return new Consumer($client);
        });

        $this->app->singleton(SatEstadoCfdiService::class, function ($app) {
            return new SatEstadoCfdiService(
                $app->make(Consumer::class),
                $app->make(EstadoCfdiResponseNormalizerService::class)
            );
        });

        $this->app->alias(SatEstadoCfdiService::class, 'sat-estado-cfdi');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/sat-estado-cfdi.php' => config_path('sat-estado-cfdi.php'),
        ], 'sat-estado-cfdi-config');

        $this->registerHttpRoutes();
    }

    private function registerHttpRoutes(): void
    {
        if (! config('sat-estado-cfdi.expose_routes', false)) {
            return;
        }

        Route::group([
            'prefix' => config('sat-estado-cfdi.route_prefix', 'api'),
            'middleware' => $this->routeMiddleware(),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * @return array<int, string>
     */
    private function routeMiddleware(): array
    {
        $middleware = config('sat-estado-cfdi.middleware', 'api');

        if (is_string($middleware)) {
            $middleware = array_map('trim', explode(',', $middleware));
        }

        if (! is_array($middleware)) {
            return ['api'];
        }

        return array_values(array_filter(
            $middleware,
            static fn (mixed $value): bool => is_string($value) && trim($value) !== ''
        ));
    }
}
