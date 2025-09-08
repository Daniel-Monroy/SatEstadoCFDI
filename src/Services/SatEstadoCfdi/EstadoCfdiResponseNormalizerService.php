<?php

namespace DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiResponseDto;

class EstadoCfdiResponseNormalizerService
{
    public function toDto(object $status, string $id): EstadoCfdiResponseDto
    {
        $isActive = $status->document->isActive();
        $isCancelled = $status->document->isCancelled();

        return new EstadoCfdiResponseDto(
            ok: true,
            id: $id,
            status: $isActive ? 'active' : 'cancelled',
            message: $isActive
                ? 'The CFDI is active and valid.'
                : 'The CFDI was cancelled',
            cancelabilidad: $this->normalizeCancelability($status),
            cancelacion: $this->normalizeCancellation($status),
            flags: [
                'isActive' => $isActive,
                'isCancelled' => $isCancelled,
                'isPendingCancel' => $status->cancellation->isPending(),
            ],
            raw: [
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
                'efos' => $status->efos->value ?? null,
            ]
        );
    }

    private function normalizeCancelability(object $status): string
    {
        return match (true) {
            $status->cancellable->isCancellableByDirectCall() => 'sin_aceptacion',
            $status->cancellable->isCancellableByApproval() => 'con_aceptacion',
            default => 'no_cancelable'
        };
    }

    private function normalizeCancellation(object $status): string
    {
        return match (true) {
            $status->cancellation->isPending() => 'en_proceso',
            $status->cancellation->isCancelledByDirectCall() => 'aceptada',
            $status->cancellation->isCancelledByApproval() => 'aceptada',
            $status->cancellation->isCancelledByExpiration() => 'vencida',
            $status->cancellation->isDisapproved() => 'rechazada',
            $status->cancellation->isUndefined() => 'indefinida',
            default => 'no_cancelado',
        };
    }
}
