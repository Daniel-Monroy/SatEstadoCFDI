<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Controllers;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiNotFoundDto;
use DanielMonroy\SatEstadoCfdi\Http\Request\ConsultarEstadoCfdiRequest;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\EstadoCfdiResponseNormalizerService;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ConsultarEstadoCfdiController extends Controller
{
    public function __invoke(
        ConsultarEstadoCfdiRequest $request,
        SatEstadoCfdiService       $cfdiService,
    ): JsonResponse
    {
        $response = $request->hasFile('xml')
            ? $cfdiService->consultFromXmlPath($request->file('xml')->getRealPath())
            : $cfdiService->consultByExpression((string)$request->string('expression'));

        return response()->json(
            $response,
            $response instanceof EstadoCfdiNotFoundDto ? 404 : 200
        );
    }
}
