<?php

namespace App\Enums;

/**
 * Type de vente effectuée
 */
enum SaleType: string
{
    case ACHAT_DIRECT = 'achat_direct';
    case TROC = 'troc'; // Toujours avec complément espèces

    public function label(): string
    {
        return match ($this) {
            self::ACHAT_DIRECT => 'Achat direct',
            self::TROC => 'Troc avec reprise',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACHAT_DIRECT => 'Paiement intégral en espèces',
            self::TROC => 'Reprise d\'ancien appareil + complément espèces',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACHAT_DIRECT => 'blue',
            self::TROC => 'purple',
        };
    }

    /**
     * Retourne tous les types de vente sous forme d'options pour select
     */
    public static function options(): array
    {
        return collect(self::cases())->map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
            'description' => $case->description(),
        ])->toArray();
    }
}
