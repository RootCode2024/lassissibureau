<?php

namespace App\Enums;

enum StockMovementType: string
{
    // Entrées de stock
    case RECEPTION_FOURNISSEUR = 'reception_fournisseur';
    case RETOUR_CLIENT = 'retour_client';
    case CORRECTION_INVENTAIRE_PLUS = 'correction_inventaire_plus';

        // Sorties de stock
    case VENTE = 'vente';
    case CASSE = 'casse';
    case VOL = 'vol';
    case PERTE = 'perte';
    case RETOUR_FOURNISSEUR = 'retour_fournisseur';
    case CORRECTION_INVENTAIRE_MOINS = 'correction_inventaire_moins';
    case ECHEANCE = 'echeance'; // Produits périmés
    case ECHANTILLON = 'echantillon'; // Échantillons gratuits

        // Consignation revendeurs
    case DEPOT_REVENDEUR = 'depot_revendeur'; // Revendeur prend des produits
    case RETOUR_REVENDEUR = 'retour_revendeur'; // Revendeur ramène des invendus

    /**
     * Détermine si ce type augmente le stock
     */
    public function isIncrement(): bool
    {
        return in_array($this, [
            self::RECEPTION_FOURNISSEUR,
            self::RETOUR_CLIENT,
            self::CORRECTION_INVENTAIRE_PLUS,
            self::RETOUR_REVENDEUR, // Revendeur ramène des invendus
        ]);
    }

    /**
     * Détermine si ce type diminue le stock
     */
    public function isDecrement(): bool
    {
        return !$this->isIncrement();
    }

    /**
     * Obtenir le label français
     */
    public function label(): string
    {
        return match ($this) {
            self::RECEPTION_FOURNISSEUR => 'Réception fournisseur',
            self::RETOUR_CLIENT => 'Retour client',
            self::CORRECTION_INVENTAIRE_PLUS => 'Correction inventaire (+)',
            self::VENTE => 'Vente',
            self::CASSE => 'Casse',
            self::VOL => 'Vol',
            self::PERTE => 'Perte',
            self::RETOUR_FOURNISSEUR => 'Retour fournisseur',
            self::CORRECTION_INVENTAIRE_MOINS => 'Correction inventaire (-)',
            self::ECHEANCE => 'Produit périmé',
            self::ECHANTILLON => 'Échantillon gratuit',
            self::DEPOT_REVENDEUR => 'Dépôt revendeur',
            self::RETOUR_REVENDEUR => 'Retour revendeur',
        };
    }

    /**
     * Obtenir la couleur badge pour l'UI
     */
    public function color(): string
    {
        return match ($this) {
            self::RECEPTION_FOURNISSEUR,
            self::RETOUR_CLIENT,
            self::RETOUR_REVENDEUR => 'green',

            self::VENTE => 'blue',

            self::CASSE,
            self::VOL,
            self::PERTE,
            self::ECHEANCE => 'red',

            self::RETOUR_FOURNISSEUR,
            self::ECHANTILLON,
            self::DEPOT_REVENDEUR => 'orange',

            self::CORRECTION_INVENTAIRE_PLUS,
            self::CORRECTION_INVENTAIRE_MOINS => 'purple',
        };
    }

    /**
     * Obtenir l'icône pour l'UI
     */
    public function icon(): string
    {
        return match ($this) {
            self::RECEPTION_FOURNISSEUR => 'truck',
            self::RETOUR_CLIENT => 'arrow-left-circle',
            self::VENTE => 'shopping-cart',
            self::CASSE => 'trash-2',
            self::VOL => 'alert-triangle',
            self::PERTE => 'x-circle',
            self::RETOUR_FOURNISSEUR => 'arrow-right-circle',
            self::CORRECTION_INVENTAIRE_PLUS,
            self::CORRECTION_INVENTAIRE_MOINS => 'edit',
            self::ECHEANCE => 'calendar-x',
            self::ECHANTILLON => 'gift',
            self::DEPOT_REVENDEUR => 'package-minus',
            self::RETOUR_REVENDEUR => 'package-plus',
        };
    }

    /**
     * Nécessite une justification obligatoire
     */
    public function requiresJustification(): bool
    {
        return in_array($this, [
            self::CASSE,
            self::VOL,
            self::PERTE,
            self::CORRECTION_INVENTAIRE_PLUS,
            self::CORRECTION_INVENTAIRE_MOINS,
        ]);
    }

    /**
     * Types accessibles par le vendeur
     */
    public static function forVendeur(): array
    {
        return [
            self::VENTE,
            self::RETOUR_CLIENT,
        ];
    }

    /**
     * Types accessibles par l'admin uniquement
     */
    public static function forAdminOnly(): array
    {
        return [
            self::RECEPTION_FOURNISSEUR,
            self::CASSE,
            self::VOL,
            self::PERTE,
            self::RETOUR_FOURNISSEUR,
            self::CORRECTION_INVENTAIRE_PLUS,
            self::CORRECTION_INVENTAIRE_MOINS,
            self::ECHEANCE,
            self::ECHANTILLON,
            self::DEPOT_REVENDEUR,
            self::RETOUR_REVENDEUR,
        ];
    }

    /**
     * Grouper par catégorie pour l'UI
     */
    public static function grouped(): array
    {
        return [
            'Entrées' => [
                self::RECEPTION_FOURNISSEUR,
                self::RETOUR_CLIENT,
                self::RETOUR_REVENDEUR,
            ],
            'Sorties normales' => [
                self::VENTE,
                self::RETOUR_FOURNISSEUR,
                self::ECHANTILLON,
                self::DEPOT_REVENDEUR,
            ],
            'Pertes et avaries' => [
                self::CASSE,
                self::VOL,
                self::PERTE,
                self::ECHEANCE,
            ],
            'Corrections' => [
                self::CORRECTION_INVENTAIRE_PLUS,
                self::CORRECTION_INVENTAIRE_MOINS,
            ],
        ];
    }

    /**
     * Options pour select avec groupes
     */
    public static function optionsGrouped(): array
    {
        $result = [];
        foreach (self::grouped() as $group => $types) {
            $result[$group] = array_map(
                fn(self $type) => [
                    'value' => $type->value,
                    'label' => $type->label(),
                    'color' => $type->color(),
                    'icon' => $type->icon(),
                ],
                $types
            );
        }
        return $result;
    }

    /**
     * Obtenir tous les types
     */
    public static function all(): array
    {
        return self::cases();
    }
}
