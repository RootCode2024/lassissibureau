<?php

namespace App\Enums;

/**
 * État actuel d'un produit dans le système
 */
enum ProductStatus: string
{
    case STOCK_BOUTIQUE = 'stock_boutique';
    case VENDU = 'vendu';
    case CHEZ_REVENDEUR = 'chez_revendeur';
    case RETOUR_CLIENT = 'retour_client';
    case A_REPARER = 'a_reparer';
    case REPARE = 'repare';
    case RETOUR_FOURNISSEUR = 'retour_fournisseur';

    public function label(): string
    {
        return match ($this) {
            self::STOCK_BOUTIQUE => 'En stock boutique',
            self::VENDU => 'Vendu',
            self::CHEZ_REVENDEUR => 'Chez revendeur',
            self::RETOUR_CLIENT => 'Retour client',
            self::A_REPARER => 'À réparer',
            self::REPARE => 'Réparé',
            self::RETOUR_FOURNISSEUR => 'Retourné fournisseur',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::STOCK_BOUTIQUE, self::REPARE => 'green',
            self::VENDU => 'blue',
            self::CHEZ_REVENDEUR => 'orange',
            self::RETOUR_CLIENT, self::A_REPARER => 'yellow',
            self::RETOUR_FOURNISSEUR => 'red',
        };
    }

    /**
     * Indique si le produit est disponible à la vente
     */
    public function isAvailable(): bool
    {
        return in_array($this, [
            self::STOCK_BOUTIQUE,
            self::REPARE,
        ]);
    }

    /**
     * Indique si le produit compte dans le stock physique
     */
    public function isInStock(): bool
    {
        return in_array($this, [
            self::STOCK_BOUTIQUE,
            self::RETOUR_CLIENT,
            self::A_REPARER,
            self::REPARE,
        ]);
    }
}
