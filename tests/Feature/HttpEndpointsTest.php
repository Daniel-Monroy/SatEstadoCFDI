<?php

use DanielMonroy\SatEstadoCfdi\Facades\SatEstado;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use DanielMonroy\SatEstadoCfdi\Tests\Support\FakeConsumerClient;
use DanielMonroy\SatEstadoCfdi\Tests\Support\Fixtures;
use Illuminate\Http\UploadedFile;
use PhpCfdi\SatEstadoCfdi\Consumer;

it('exposes the health endpoint', function (): void {
    $this->getJson('/api/cfdi/estatus')
        ->assertOk()
        ->assertExactJson([
            'ok' => true,
            'status' => 'available',
            'message' => 'El servicio del paquete está disponible.',
        ]);
});

it('returns a successful response for a valid expression', function (): void {
    app()->instance(Consumer::class, new Consumer(new FakeConsumerClient(Fixtures::foundResponse())));
    app()->forgetInstance(SatEstadoCfdiService::class);

    $this->postJson('/api/cfdi/estado', [
        'expression' => 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45&fe=5678',
    ])
        ->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('status', 'active')
        ->assertJsonPath('id', '12345678-1234-1234-1234-123456789012');
});

it('returns not found when the cfdi does not exist', function (): void {
    app()->instance(Consumer::class, new Consumer(new FakeConsumerClient(Fixtures::notFoundResponse())));
    app()->forgetInstance(SatEstadoCfdiService::class);

    $this->postJson('/api/cfdi/estado', [
        'expression' => 'id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45',
    ])
        ->assertNotFound()
        ->assertJsonPath('status', 'not_found');
});

it('returns validation errors when required expression fields are missing', function (): void {
    $this->postJson('/api/cfdi/estado', [
        'expression' => 'id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('expression');
});

it('returns validation errors for malformed xml uploads', function (): void {
    $file = UploadedFile::fake()->createWithContent('cfdi.xml', '<cfdi:Comprobante>');

    $this->post('/api/cfdi/estado', [
        'xml' => $file,
    ])
        ->assertUnprocessable()
        ->assertJsonPath('status', 'invalid_xml');
});

it('accepts valid xml uploads without failing on file path resolution', function (): void {
    app()->instance(Consumer::class, new Consumer(new FakeConsumerClient(Fixtures::foundResponse())));
    app()->forgetInstance(SatEstadoCfdiService::class);

    $file = UploadedFile::fake()->createWithContent('cfdi.xml', Fixtures::cfdi40Xml());

    $this->post('/api/cfdi/estado', [
        'xml' => $file,
    ])
        ->assertOk()
        ->assertJsonPath('ok', true)
        ->assertJsonPath('id', '12345678-1234-1234-1234-123456789012');
});

it('can query using the facade', function (): void {
    app()->instance(Consumer::class, new Consumer(new FakeConsumerClient(Fixtures::foundResponse())));
    app()->forgetInstance(SatEstadoCfdiService::class);

    $response = SatEstado::consultByExpression(
        'id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45'
    );

    expect($response->ok)->toBeTrue()
        ->and($response->id)->toBe('12345678-1234-1234-1234-123456789012');
});
