<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Controllers;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiNotFoundDto;
use DanielMonroy\SatEstadoCfdi\Http\Request\ConsultarEstadoCfdiRequest;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi\SatEstadoCfdiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use RuntimeException;

class ConsultarEstadoCfdiController extends Controller
{
    public function __invoke(
        ConsultarEstadoCfdiRequest $request,
        SatEstadoCfdiService $cfdiService,
    ): JsonResponse {
        try {
            $response = $request->hasFile('xml')
                ? $cfdiService->consultFromXmlPath($request->file('xml')->getRealPath())
                : $cfdiService->consultByExpression((string) $request->string('expression'));
        } catch (RuntimeException $exception) {
            return response()->json([
                'ok' => false,
                'status' => 'invalid_xml',
                'message' => $exception->getMessage(),
            ], 422);
        }

        return response()->json(
            $response,
            $response instanceof EstadoCfdiNotFoundDto ? 404 : 200
        );
    }
}
