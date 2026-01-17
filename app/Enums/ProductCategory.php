<?php

namespace App\Enums;

/**
 * Catégories de produits
 */
enum ProductCategory: string
{
    case TELEPHONE = 'telephone';
    case TABLETTE = 'tablette';
    case ACCESSOIRE = 'accessoire';

    public function label(): string
    {
        return match ($this) {
            self::TELEPHONE => 'Téléphone',
            self::TABLETTE => 'Tablette',
            self::ACCESSOIRE => 'Accessoire',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TELEPHONE => 'smartphone',
            self::TABLETTE => 'tablet',
            self::ACCESSOIRE => 'package',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TELEPHONE => 'blue',
            self::TABLETTE => 'purple',
            self::ACCESSOIRE => 'green',
        };
    }

    /**
     * Détermine si cette catégorie nécessite un IMEI
     */
    public function requiresImei(): bool
    {
        return in_array($this, [
            self::TELEPHONE,
            self::TABLETTE,
        ]);
    }

    /**
     * Détermine si cette catégorie nécessite un numéro de série
     */
    public function requiresSerialNumber(): bool
    {
        return $this === self::ACCESSOIRE;
    }

    /**
     * Obtenir toutes les catégories sous forme de tableau
     */
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtenir les options pour un select
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }
}