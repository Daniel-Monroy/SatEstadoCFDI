<?php

declare(strict_types=1);

namespace DanielMonroy\SatEstadoCfdi\Services\SatEstadoCfdi;

use DanielMonroy\SatEstadoCfdi\DTOs\EstadoCfdiResponseDto;
use PhpCfdi\SatEstadoCfdi\CfdiStatus;

class EstadoCfdiResponseNormalizerService
{
    public function toDto(CfdiStatus $status, string $id): EstadoCfdiResponseDto
    {
        $isActive = $status->document->isActive();
        $isCancelled = $status->document->isCancelled();
        $isPendingCancel = $status->cancellation->isPending();
        $cancellation = $status->cancellation;
        $cancellable = $status->cancellable;

        return new EstadoCfdiResponseDto(
            ok: true,
            id: $id,
            status: $isActive ? 'active' : 'cancelled',
            message: $isActive
                ? 'El CFDI se encuentra vigente y es válido.'
                : 'El CFDI fue cancelado.',
            cancelabilidad: $this->normalizeCancelability($status),
            cancelacion: $this->normalizeCancellation($status),
            flags: [
                'isActive' => $isActive,
                'isCancelled' => $isCancelled,
                'isPendingCancel' => $isPendingCancel,
            ],
            raw: [
                'query' => [
                    'isFound' => $status->query->isFound(),
                ],
                'document' => [
                    'isActive' => $isActive,
                    'isCancelled' => $isCancelled,
                ],
                'cancellable' => [
                    'isCancellableByDirect' => $cancellable->isCancellableByDirectCall(),
                    'isCancellableByApproval' => $cancellable->isCancellableByApproval(),
                ],
                'cancellation' => [
                    'isCancelledByDirect' => $cancellation->isCancelledByDirectCall(),
                    'isCancelledByApproval' => $cancellation->isCancelledByApproval(),
                    'isCancelledByExpiration' => $cancellation->isCancelledByExpiration(),
                    'isPending' => $isPendingCancel,
                    'isDisapproved' => $cancellation->isDisapproved(),
                    'isUndefined' => $cancellation->isUndefined(),
                ],
                'efos' => $status->efos->name,
            ],
        );
    }

    private function normalizeCancelability(CfdiStatus $status): string
    {
        return match (true) {
            $status->cancellable->isCancellableByDirectCall() => 'sin_aceptacion',
            $status->cancellable->isCancellableByApproval() => 'con_aceptacion',
            default => 'no_cancelable',
        };
    }

    private function normalizeCancellation(CfdiStatus $status): string
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
