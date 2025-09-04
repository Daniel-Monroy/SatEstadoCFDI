<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Controllers;

use DanielMonroy\SatEstadoCfdi\Http\Request\ConsultarEstadoCfdiRequest;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdiService;
use Illuminate\Routing\Controller;

class ConsultarEstadoCfdiController extends Controller
{
    public function __invoke(ConsultarEstadoCfdiRequest $request, SatEstadoCfdiService $service)
    {
        if ($request->file('xml')) {
            $path = $request->file('xml')->getRealPath();
            $status = $service->consultFromXmlPath($path);
        } else {
            $status = $service->consultByExpression((string)$request->string('expression'));
        }

        return response()->json([
            'codigo_estatus' => $status->query->value,
            'documento' => $status->document->value,
            'es_cancelable' => $status->cancellable->value,
            'estatus_cancelacion' => $status->cancellation->value,
            'efos' => $status->efos->value ?? null,
            'helpers' => [
                'isActive' => $status->document->isActive(),
                'isCancelled' => $status->document->isCancelled(),
                'isPendingCancel' => $status->cancellation->isPending(),
            ],
        ]);
    }
}
