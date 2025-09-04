<?php

namespace DanielMonroy\SatEstadoCfdi\Http\Controllers;

use DanielMonroy\SatEstadoCfdi\Http\Request\ConsultarEstadoCfdiRequest;
use DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ConsultarEstadoCfdiController extends Controller
{
    /**
     * Controlador para consultar el estado de un CFDI (Comprobante Fiscal Digital por Internet).
     *
     * Este método maneja la solicitud para consultar el estado de un CFDI, ya sea mediante
     * un archivo XML o una expresión de consulta. Devuelve una respuesta JSON con el estado
     * del CFDI, incluyendo información sobre su cancelabilidad, estado de cancelación y
     * otros indicadores relevantes.
     *
     * @param ConsultarEstadoCfdiRequest $request Solicitud HTTP que contiene los datos necesarios
     *                                             para realizar la consulta (archivo XML o expresión).
     * @param SatEstadoCfdiService $service Servicio que realiza la consulta al SAT para obtener
     *                                       el estado del CFDI.
     *
     * @return JsonResponse Respuesta JSON con el estado del CFDI, incluyendo:
     *                                        - `ok`: Indica si la consulta fue exitosa.
     *                                        - `estatus`: Estado del CFDI (activo o cancelado).
     *                                        - `message`: Mensaje descriptivo del estado.
     *                                        - `cancelabilidad`: Identificador del tipo de cancelabilidad.
     *                                        - `cancelacion`: Identificador del estado de cancelación.
     *                                        - `flags`: Indicadores booleanos sobre el estado del CFDI.
     *                                        - `raw`: Datos sin procesar obtenidos del servicio.
     */
    public function __invoke(ConsultarEstadoCfdiRequest $request, SatEstadoCfdiService $service)
    {
        if ($request->file('xml')) {
            $path = $request->file('xml')->getRealPath();
            $status = $service->consultFromXmlPath($path);
        } else {
            $status = $service->consultByExpression((string)$request->string('expression'));
        }

        $found = $status->query->isFound();

        if (!$found) {
            return response()->json([
                'ok' => false,
                'estatus' => 'not_found',
                'message' => 'El CFDI no fue encontrado.',
                'data' => [
                    'query' => [
                        'isFound' => false,
                    ],
                ],
            ], 404);
        }

        $isActive = $status->document->isActive();
        $isCancelled = $status->document->isCancelled();

        $cancelability = $this->normalizeCancelability($status);
        $cancelStatus = $this->normalizeCancellation($status);

        return response()->json([
            'ok' => true,
            'estatus' => $isActive ? 'activo' : 'cancelado',
            'message' => $isActive ? 'El CFDI es válido y está activo.' : 'El CFDI ha sido cancelado.',

            'cancelabilidad' => $cancelability['slug'],
            'cancelacion' => $cancelStatus['slug'],

            'flags' => [
                'isActive' => $isActive,
                'isCancelled' => $isCancelled,
                'isPendingCancel' => $status->cancellation->isPending(),
            ],

            'raw' => [
                'query' => [
                    'isFound' => $status->query->isFound(),
                ],
                'document' => [
                    'isActive' => $status->document->isActive(),
                    'isCancelled' => $status->document->isCancelled(),
                ],
                'cancellable' => [
                    'isCancellableByDirect' => $status->cancellable->isCancellableByDirectCall(),
                    'isCancellableByApproval' => $status->cancellable->isCancellableByApproval(),
                ],
                'cancellation' => [
                    'isCancelledByDirect' => $status->cancellation->isCancelledByDirectCall(),
                    'isCancelledByApproval' => $status->cancellation->isCancelledByApproval(),
                    'isCancelledByExpiration' => $status->cancellation->isCancelledByExpiration(),
                    'isPending' => $status->cancellation->isPending(),
                    'isDisapproved' => $status->cancellation->isDisapproved(),
                    'isUndefined' => $status->cancellation->isUndefined(),
                ],
                /*
                 * Validación EFOS (Empresas que Facturan Operaciones Simuladas).
                 *
                 * Este fragmento valida si el RFC emisor está en la lista de EFOS, es decir, empresas
                 * que facturan operaciones simuladas. La validación se realiza con base en el código
                 * reportado por el SAT:
                 * - Included: Cuando el SAT reporta un código distinto de 200 o 201 (el RFC está en lista negra).
                 * - Excluded: Cuando el SAT reporta 200 o 201 (el RFC no está en lista negra).
                 *
                 * En términos prácticos, responde a la pregunta:
                 * “¿El emisor de la factura está en la lista negra del SAT?”
                 */
                'efos' => $status->efos->value ?? null,
            ],
        ]);
    }

    /**
     * Normaliza la cancelabilidad de un CFDI a un formato legible.
     *
     * Este método determina si un CFDI es cancelable y bajo qué condiciones:
     * - Sin aceptación: El emisor puede cancelar el CFDI sin necesidad de que el receptor acepte.
     * - Con aceptación: El emisor puede solicitar la cancelación, pero el receptor debe aprobar
     *   la solicitud para que el CFDI sea cancelado.
     * - No cancelable: El CFDI no puede ser cancelado.
     *
     * @param object $status Objeto que contiene el estado del CFDI, incluyendo información
     *                       sobre su cancelabilidad.
     *
     * @return array Un arreglo asociativo con dos claves:
     *               - `slug`: Un identificador legible para la cancelabilidad (sin_aceptacion, con_aceptacion, no_cancelable).
     *               - `label`: Una descripción legible de la cancelabilidad.
     */
    private function normalizeCancelability($status): array
    {
        if ($status->cancellable->isCancellableByDirectCall()) {
            return [
                'slug' => 'sin_aceptacion',
                'label' => 'Cancelable sin aceptación',
            ];
        }
        if ($status->cancellable->isCancellableByApproval()) {
            return [
                'slug' => 'con_aceptacion',
                'label' => 'Cancelable con aceptación',
            ];
        }
        return ['slug' => 'no_cancelable', 'label' => 'No cancelable'];
    }

    /**
     * Normaliza el estado de cancelación de un CFDI a un formato legible.
     *
     * Este método evalúa el estado de cancelación de un CFDI y lo traduce a un formato
     * comprensible, proporcionando un identificador (`slug`) y una descripción (`label`).
     *
     * Los posibles estados de cancelación son:
     * - `en_proceso`: La cancelación está en proceso.
     * - `aceptada`: La cancelación fue aceptada (puede ser sin aceptación o con aceptación).
     * - `vencida`: La cancelación ocurrió porque se venció el plazo.
     * - `rechazada`: La solicitud de cancelación fue rechazada.
     * - `indefinida`: El estado de cancelación no es claro o no entra en ninguna categoría.
     * - `no_cancelado`: El documento está activo y no hay proceso de cancelación.
     *
     * @param object $status Objeto que contiene el estado del CFDI, incluyendo información
     *                       sobre su cancelación.
     *
     * @return array Un arreglo asociativo con dos claves:
     *               - `slug`: Un identificador legible para el estado de cancelación.
     *               - `label`: Una descripción legible del estado de cancelación.
     */
    private function normalizeCancellation(object $status): array
    {
        if ($status->cancellation->isPending()) {
            return ['slug' => 'en_proceso', 'label' => 'Cancelación en proceso'];
        }
        if ($status->cancellation->isCancelledByDirectCall()) {
            return ['slug' => 'aceptada', 'label' => 'Cancelación aceptada (sin aceptación)'];
        }
        if ($status->cancellation->isCancelledByApproval()) {
            return ['slug' => 'aceptada', 'label' => 'Cancelación aceptada (con aceptación)'];
        }
        if ($status->cancellation->isCancelledByExpiration()) {
            return ['slug' => 'vencida', 'label' => 'Cancelación por vencimiento'];
        }
        if ($status->cancellation->isDisapproved()) {
            return ['slug' => 'rechazada', 'label' => 'Solicitud de cancelación rechazada'];
        }
        if ($status->cancellation->isUndefined()) {
            return ['slug' => 'indefinida', 'label' => 'Cancelación indefinida'];
        }
        return ['slug' => 'no_cancelado', 'label' => 'No cancelado'];
    }
}
