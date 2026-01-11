<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case VENDEUR = 'vendeur';

    /**
     * Obtenir le label français
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrateur',
            self::VENDEUR => 'Vendeur',
        };
    }

    /**
     * Obtenir toutes les permissions associées au rôle
     */
    public function permissions(): array
    {
        return match ($this) {
            self::ADMIN => [
                // Gestion utilisateurs
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',

                // Gestion produits
                'products.view',
                'products.create',
                'products.edit',
                'products.delete',

                // Gestion stock
                'stock.view',
                'stock.entry',
                'stock.exit',
                'stock.adjustment',
                'stock.return',

                // Rapports
                'reports.view',
                'reports.export',

                // Catégories
                'categories.manage',
            ],

            self::VENDEUR => [
                // Lecture seulement pour produits
                'products.view',

                // Mouvements de stock limités
                'stock.view',
                'stock.exit', // Ventes uniquement
                'stock.return', // Retours clients
            ],
        };
    }

    /**
     * Obtenir tous les rôles
     */
    public static function all(): array
    {
        return [
            self::ADMIN,
            self::VENDEUR,
        ];
    }

    /**
     * Obtenir les options pour un select
     */
    public static function options(): array
    {
        return array_map(
            fn(self $role) => [
                'value' => $role->value,
                'label' => $role->label(),
            ],
            self::all()
        );
    }
}
