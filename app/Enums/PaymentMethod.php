<?php

namespace App\Enums;

/**
 * Méthodes de paiement disponibles
 */
enum PaymentMethod: string
{
    case CASH = 'cash';
    case MOBILE_MONEY = 'mobile_money';
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK = 'check';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Espèces',
            self::MOBILE_MONEY => 'Mobile Money',
            self::BANK_TRANSFER => 'Virement bancaire',
            self::CHECK => 'Chèque',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CASH => 'banknote',
            self::MOBILE_MONEY => 'smartphone',
            self::BANK_TRANSFER => 'building-2',
            self::CHECK => 'file-text',
        };
    }

    /**
     * Options pour select
     */
    public static function options(): array
    {
        return array_map(
            fn(self $method) => [
                'value' => $method->value,
                'label' => $method->label(),
                'icon' => $method->icon(),
            ],
            self::cases()
        );
    }
}
