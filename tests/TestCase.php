<?php

namespace DanielMonroy\SatEstadoCfdi\Tests;

use DanielMonroy\SatEstadoCfdi\SatEstadoCfdiServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            SatEstadoCfdiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('cache.default', 'array');
        $app['config']->set('sat-estado-cfdi.expose_routes', true);
        $app['config']->set('sat-estado-cfdi.route_prefix', 'api');
        $app['config']->set('sat-estado-cfdi.middleware', []);
    }
}
