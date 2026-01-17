<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Observer pour invalider le cache lors des changements de produits
 */
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearConditionsCache($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Invalider le cache si la condition a changé
        if ($product->isDirty('condition')) {
            $this->clearConditionsCache($product);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearConditionsCache($product);
    }

    /**
     * Invalider le cache des conditions pour la catégorie du produit
     */
    private function clearConditionsCache(Product $product): void
    {
        // Charger le modèle si nécessaire
        $product->loadMissing('productModel');

        if ($product->productModel && $product->productModel->category->value) {
            Cache::forget("conditions_{$product->productModel->category->value}");
        }
    }
}
