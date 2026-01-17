<?php

namespace App\Enums;

/**
 * Statut de paiement d'une vente
 */
enum PaymentStatus: string
{
    case UNPAID = 'unpaid';      // Pas encore payé
    case PARTIAL = 'partial';    // Partiellement payé
    case PAID = 'paid';          // Payé intégralement

    public function label(): string
    {
        return match ($this) {
            self::UNPAID => 'Non payé',
            self::PARTIAL => 'Partiellement payé',
            self::PAID => 'Payé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNPAID => 'red',
            self::PARTIAL => 'orange',
            self::PAID => 'green',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::UNPAID => 'bg-red-100 text-red-800 border-red-200',
            self::PARTIAL => 'bg-amber-100 text-amber-800 border-amber-200',
            self::PAID => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::UNPAID => 'x-circle',
            self::PARTIAL => 'clock',
            self::PAID => 'check-circle',
        };
    }

    /**
     * Vérifier si le paiement est complet
     */
    public function isFullyPaid(): bool
    {
        return $this === self::PAID;
    }

    /**
     * Vérifier si le paiement est en attente
     */
    public function isPending(): bool
    {
        return in_array($this, [self::UNPAID, self::PARTIAL]);
    }

    /**
     * Options pour select
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'icon' => $status->icon(),
            ],
            self::cases()
        );
    }
}
