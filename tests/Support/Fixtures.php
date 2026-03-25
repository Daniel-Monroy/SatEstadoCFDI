<?php

namespace DanielMonroy\SatEstadoCfdi\Tests\Support;

final class Fixtures
{
    /**
     * @return array<string, string>
     */
    public static function foundResponse(): array
    {
        return [
            'CodigoEstatus' => 'S - Comprobante obtenido satisfactoriamente.',
            'Estado' => 'Vigente',
            'EsCancelable' => 'Cancelable sin aceptación',
            'EstatusCancelacion' => 'En proceso',
            'ValidacionEFOS' => '100',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function notFoundResponse(): array
    {
        return [
            'CodigoEstatus' => 'N - 601: Resultados no encontrados.',
            'Estado' => 'No Encontrado',
            'EsCancelable' => 'No cancelable',
            'EstatusCancelacion' => 'No cancelable',
            'ValidacionEFOS' => '100',
        ];
    }

    public static function cfdi40Xml(): string
    {
        return <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/4" xmlns:tfd="http://www.sat.gob.mx/TimbreFiscalDigital" Version="4.0" Total="123.45" Sello="ABCD12345678">
  <cfdi:Emisor Rfc="AAA010101AAA" />
  <cfdi:Receptor Rfc="BBB010101BBB" />
  <cfdi:Complemento>
    <tfd:TimbreFiscalDigital UUID="12345678-1234-1234-1234-123456789012" />
  </cfdi:Complemento>
</cfdi:Comprobante>
XML;
    }
}
