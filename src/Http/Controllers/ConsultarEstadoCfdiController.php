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
        ConsultarEstadoCfdiRequest          $request,
        SatEstadoCfdiService                $service,
        EstadoCfdiResponseNormalizerService $normalizer
    ): JsonResponse
    {
        $cfdiResponse = $request->file('xml')
            ? $service->consultFromXmlPath($request->file('xml')->getRealPath())
            : $service->consultByExpression((string)$request->string('expression'));

        if (!$cfdiResponse->status->query->isFound()) {
            return response()->json(new EstadoCfdiNotFoundDto(), 404);
        }

        $dto = $normalizer->toDto($cfdiResponse->status, $cfdiResponse->id ?? 'unknown');
        return response()->json($dto);
    }
}
