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
        if ($request->hasFile('xml')) {
            try {
                $response = $cfdiService->consultFromXmlPath($request->file('xml')->getRealPath());
            } catch (RuntimeException $exception) {
                return response()->json([
                    'ok' => false,
                    'status' => 'invalid_xml',
                    'message' => $exception->getMessage(),
                ], 422);
            }
        } else {
            $response = $cfdiService->consultByExpression((string) $request->string('expression'));
        }

        return response()->json(
            $response,
            $response instanceof EstadoCfdiNotFoundDto ? 404 : 200
        );
    }
}
