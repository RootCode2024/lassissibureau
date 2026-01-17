<?php

namespace App\Services;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Créer un mouvement de stock.
     */
    public function createMovement(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::findOrFail($data['product_id']);

            // Capturer le statut avant si non fourni
            if (! isset($data['state_before'])) {
                $data['state_before'] = $product->state->value;
            }
            if (! isset($data['location_before'])) {
                $data['location_before'] = $product->location->value;
            }

            // Créer le mouvement
            $movement = StockMovement::create($data);

            // Mettre à jour le statut du produit si fourni
            $updates = ['updated_by' => $data['user_id']];

            if (isset($data['state_after'])) {
                $updates['state'] = $data['state_after'];
            }
            if (isset($data['location_after'])) {
                $updates['location'] = $data['location_after'];
            }

            $product->update($updates);

            return $movement->fresh(['product.productModel', 'user', 'sale', 'reseller']);
        });
    }

    /**
     * Obtenir l'historique des mouvements d'un produit.
     */
    public function getProductHistory(Product $product)
    {
        return $product->stockMovements()
            ->with(['user', 'sale', 'reseller', 'relatedProduct'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Obtenir les mouvements de stock pour une période.
     */
    public function getMovementsByPeriod($startDate, $endDate, ?string $type = null)
    {
        $query = StockMovement::with(['product.productModel', 'user'])
            ->betweenDates($startDate, $endDate);

        if ($type) {
            $movementType = StockMovementType::from($type);
            $query->byType($movementType);
        }

        return $query->latest()->get();
    }

    /**
     * Obtenir les statistiques des mouvements.
     */
    public function getMovementStats($startDate, $endDate): array
    {
        $movements = StockMovement::betweenDates($startDate, $endDate)->get();

        $increments = $movements->filter(fn ($m) => $m->isIncrement());
        $decrements = $movements->filter(fn ($m) => $m->isDecrement());

        return [
            'total_movements' => $movements->count(),
            'total_increments' => $increments->count(),
            'total_decrements' => $decrements->count(),
            'movements_by_type' => $movements->groupBy('type')->map->count(),
            'movements_by_user' => $movements->groupBy('user_id')->map->count(),
        ];
    }

    /**
     * Ajuster le stock (correction inventaire).
     */
    public function adjustStock(
        Product $product,
        int $quantity,
        string $justification,
        int $userId
    ): StockMovement {
        $type = $quantity > 0
            ? StockMovementType::CORRECTION_PLUS
            : StockMovementType::CORRECTION_MOINS;

        return $this->createMovement([
            'product_id' => $product->id,
            'type' => $type->value,
            'quantity' => abs($quantity),
            'state_after' => $product->state->value,
            'location_after' => $product->location->value,
            'justification' => $justification,
            'user_id' => $userId,
        ]);
    }

    /**
     * Envoyer un produit en réparation.
     */
    public function sendToRepair(Product $product, string $notes, int $userId): StockMovement
    {
        return $this->createMovement([
            'product_id' => $product->id,
            'type' => StockMovementType::ENVOI_REPARATION->value,
            'quantity' => 1,
            'state_after' => ProductState::A_REPARER->value,
            'location_after' => ProductLocation::EN_REPARATION->value,
            'notes' => $notes,
            'user_id' => $userId,
        ]);
    }

    /**
     * Retour de réparation.
     */
    public function returnFromRepair(Product $product, string $notes, int $userId): StockMovement
    {
        return $this->createMovement([
            'product_id' => $product->id,
            'type' => StockMovementType::RETOUR_REPARATION->value,
            'quantity' => 1,
            'state_after' => ProductState::REPARE->value,
            'location_after' => ProductLocation::BOUTIQUE->value,
            'notes' => $notes,
            'user_id' => $userId,
        ]);
    }
}
