<?php

namespace App\Enums;

/**
 * Type de vente effectuée
 */
enum SaleType: string
{
    case ACHAT_DIRECT = 'achat_direct';
    case TROC = 'troc';
    case TROC_AVEC_COMPLEMENT = 'troc_avec_complement';

    public function label(): string
    {
        return match ($this) {
            self::ACHAT_DIRECT => 'Achat direct',
            self::TROC => 'Troc (échange pur)',
            self::TROC_AVEC_COMPLEMENT => 'Troc avec complément',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACHAT_DIRECT => 'blue',
            self::TROC => 'purple',
            self::TROC_AVEC_COMPLEMENT => 'indigo',
        };
    }
}
