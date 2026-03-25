<?php

use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\EstadoCfdiResponseNormalizerService;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use DanielMonroy\SatEstadoCfdi\Tests\Support\FakeConsumerClient;
use DanielMonroy\SatEstadoCfdi\Tests\Support\Fixtures;
use Illuminate\Support\Facades\Cache;
use PhpCfdi\SatEstadoCfdi\Consumer;

it('extracts the id from a full sat verification url', function (): void {
    $service = new SatEstadoCfdiService(
        new Consumer(new FakeConsumerClient(Fixtures::foundResponse())),
        new EstadoCfdiResponseNormalizerService,
    );

    $response = $service->consultByExpression(
        'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45&fe=5678',
        0,
    );

    expect($response->id)->toBe('12345678-1234-1234-1234-123456789012');
});

it('consults from a valid xml file path', function (): void {
    $path = tempnam(sys_get_temp_dir(), 'cfdi');
    file_put_contents($path, Fixtures::cfdi40Xml());

    $service = new SatEstadoCfdiService(
        new Consumer(new FakeConsumerClient(Fixtures::foundResponse())),
        new EstadoCfdiResponseNormalizerService,
    );

    $response = $service->consultFromXmlPath($path, 0);

    @unlink($path);

    expect($response->ok)->toBeTrue()
        ->and($response->id)->toBe('12345678-1234-1234-1234-123456789012');
});

it('shares cache for equivalent sat url and query string expressions', function (): void {
    Cache::flush();

    $client = new FakeConsumerClient(Fixtures::foundResponse());
    $service = new SatEstadoCfdiService(
        new Consumer($client),
        new EstadoCfdiResponseNormalizerService
    );

    $service->consultByExpression(
        'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45&fe=5678'
    );

    $service->consultByExpression(
        'id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45&fe=5678'
    );

    expect($client->consumeCalls())->toBe(1);
});
