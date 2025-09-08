<?php

namespace DanielMonroy\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use DanielMonroy\SatEstadoCfdi\Support\GuzzleFactory;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\EstadoCfdiResponseNormalizerService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use PhpCfdi\SatEstadoCfdi\Clients\Http\HttpConsumerClient;
use PhpCfdi\SatEstadoCfdi\Clients\Http\HttpConsumerFactory;
use PhpCfdi\SatEstadoCfdi\Consumer;

class SatEstadoCfdiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/sat-estado-cfdi.php', 'sat-estado-cfdi');

        // Consumer PSR-18 (Guzzle) singleton
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

        // Facade accessor
        $this->app->alias(SatEstadoCfdiService::class, 'sat-estado-cfdi');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/sat-estado-cfdi.php' => config_path('sat-estado-cfdi.php'),
        ], 'sat-estado-cfdi-config');

        if (config('sat-estado-cfdi.expose_routes', false)) {
            Route::group([
                'prefix' => config('sat-estado-cfdi.route_prefix', 'api'),
                'middleware' => config('sat-estado-cfdi.middleware', ['api', 'auth:sanctum']),
            ], function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });
        }
    }
}
