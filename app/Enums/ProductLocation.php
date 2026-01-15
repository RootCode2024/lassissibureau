<?php

namespace App\Enums;

/**
 * Localisation physique d'un produit
 */
enum ProductLocation: string
{
    case BOUTIQUE = 'boutique';
    case CHEZ_REVENDEUR = 'chez_revendeur';
    case CHEZ_CLIENT = 'chez_client';
    case FOURNISSEUR = 'fournisseur';
    case EN_REPARATION = 'en_reparation';

    public function label(): string
    {
        return match ($this) {
            self::BOUTIQUE => 'En boutique',
            self::CHEZ_REVENDEUR => 'Chez revendeur',
            self::CHEZ_CLIENT => 'Chez client',
            self::FOURNISSEUR => 'Chez fournisseur',
            self::EN_REPARATION => 'En réparation',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::BOUTIQUE => 'success',
            self::CHEZ_REVENDEUR => 'warning',
            self::CHEZ_CLIENT => 'info',
            self::FOURNISSEUR => 'default',
            self::EN_REPARATION => 'warning',
        };
    }

    /**
     * Badge Tailwind classes
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::BOUTIQUE => 'bg-emerald-100 text-emerald-800',
            self::CHEZ_REVENDEUR => 'bg-amber-100 text-amber-800',
            self::CHEZ_CLIENT => 'bg-blue-100 text-blue-800',
            self::FOURNISSEUR => 'bg-gray-100 text-gray-800',
            self::EN_REPARATION => 'bg-purple-100 text-purple-800',
        };
    }

    /**
     * Icône Lucide
     */
    public function icon(): string
    {
        return match ($this) {
            self::BOUTIQUE => 'store',
            self::CHEZ_REVENDEUR => 'users',
            self::CHEZ_CLIENT => 'user',
            self::FOURNISSEUR => 'truck',
            self::EN_REPARATION => 'wrench',
        };
    }

    /**
     * Indique si le produit est dans le stock physique de la boutique
     */
    public function isInStock(): bool
    {
        return in_array($this, [
            self::BOUTIQUE,
            self::EN_REPARATION,
        ]);
    }

    /**
     * Indique si le produit est hors de la boutique
     */
    public function isOutside(): bool
    {
        return !$this->isInStock();
    }

    /**
     * Options pour select
     */
    public static function options(): array
    {
        return array_map(
            fn(self $location) => [
                'value' => $location->value,
                'label' => $location->label(),
                'icon' => $location->icon(),
            ],
            self::cases()
        );
    }
}
