<?php

namespace App\Services;

use App\Enums\ProductStatus;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\ProductModel;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Créer un nouveau produit avec mouvement de stock initial.
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            // Créer le produit
            $product = Product::create($data);

            // Créer le mouvement de stock initial (réception)
            $product->stockMovements()->create([
                'type' => StockMovementType::RECEPTION_FOURNISSEUR->value,
                'quantity' => 1,
                'status_before' => null,
                'status_after' => $data['status'],
                'user_id' => $data['created_by'],
                'notes' => 'Création du produit - Réception initiale',
            ]);

            return $product->fresh(['productModel', 'stockMovements']);
        });
    }

    /**
     * Mettre à jour un produit.
     */
    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);

        return $product->fresh(['productModel', 'stockMovements']);
    }

    /**
     * Mettre à jour les prix d'un produit.
     */
    public function updatePrices(Product $product, float $prixAchat, float $prixVente, int $userId): Product
    {
        $oldPrixAchat = $product->prix_achat;
        $oldPrixVente = $product->prix_vente;

        $product->update([
            'prix_achat' => $prixAchat,
            'prix_vente' => $prixVente,
            'updated_by' => $userId,
        ]);

        // Log l'activité
        activity()
            ->performedOn($product)
            ->causedBy($userId)
            ->withProperties([
                'old_prix_achat' => $oldPrixAchat,
                'new_prix_achat' => $prixAchat,
                'old_prix_vente' => $oldPrixVente,
                'new_prix_vente' => $prixVente,
            ])
            ->log('Prix mis à jour');

        return $product->fresh();
    }

    /**
     * Changer le statut d'un produit avec mouvement de stock.
     */
    public function changeStatus(
        Product $product,
        ProductStatus $newStatus,
        StockMovementType $movementType,
        int $userId,
        array $additionalData = []
    ): Product {
        $product->changeStatus($newStatus, $movementType->value, $userId, $additionalData);

        return $product->fresh(['productModel', 'stockMovements']);
    }

    /**
     * Rechercher un produit par IMEI.
     */
    public function findByImei(string $imei): ?Product
    {
        // Nettoyer l'IMEI
        $cleanImei = preg_replace('/[^0-9]/', '', $imei);

        return Product::with(['productModel', 'sale', 'stockMovements'])
            ->where('imei', $cleanImei)
            ->first();
    }

    /**
     * Obtenir les produits en stock bas.
     */
    public function getLowStockProducts()
    {
        return ProductModel::with(['productsInStock'])
            ->get()
            ->filter(fn($model) => $model->isLowStock())
            ->map(function ($model) {
                return [
                    'model' => $model,
                    'current_stock' => $model->stock_quantity,
                    'minimum_stock' => $model->stock_minimum,
                    'deficit' => $model->stock_minimum - $model->stock_quantity,
                ];
            });
    }

    /**
     * Obtenir les statistiques d'un produit.
     */
    public function getProductStats(Product $product): array
    {
        return [
            'total_movements' => $product->stockMovements()->count(),
            'benefice_potentiel' => $product->benefice_potentiel,
            'marge_percentage' => $product->marge_percentage,
            'days_in_stock' => $product->date_achat
                ? now()->diffInDays($product->date_achat)
                : null,
            'is_available' => $product->isAvailable(),
        ];
    }

    /**
     * Supprimer un produit (soft delete).
     */
    public function deleteProduct(Product $product): bool
    {
        // Vérifier que le produit n'est pas vendu ou chez un revendeur
        if (in_array($product->status, [ProductStatus::VENDU, ProductStatus::CHEZ_REVENDEUR])) {
            throw new \Exception('Impossible de supprimer un produit vendu ou chez un revendeur.');
        }

        return $product->delete();
    }
}
