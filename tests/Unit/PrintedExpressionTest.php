<?php

use DanielMonroy\SatEstadoCfdi\Support\PrintedExpression;

it('extracts params from a sat verification url', function (): void {
    $params = PrintedExpression::parse(
        'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx?id=12345678-1234-1234-1234-123456789012&re=AAA010101AAA&rr=BBB010101BBB&tt=123.45&fe=5678'
    );

    expect($params)->toMatchArray([
        'id' => '12345678-1234-1234-1234-123456789012',
        're' => 'AAA010101AAA',
        'rr' => 'BBB010101BBB',
        'tt' => '123.45',
        'fe' => '5678',
    ]);
});

it('reports missing required fields from an incomplete expression', function (): void {
    $missing = PrintedExpression::missingRequiredFields('id=123&re=AAA');

    expect($missing)->toBe(['rr', 'tt']);
});
