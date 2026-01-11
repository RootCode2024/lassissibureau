<?php

namespace App\Policies;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('products.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return $user->can('products.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('products.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {
        // Les vendeurs ne peuvent pas modifier les produits
        if ($user->isVendeur()) {
            return false;
        }

        return $user->can('products.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        // Ne peut pas supprimer un produit vendu ou chez un revendeur
        if (in_array($product->status, [
            ProductStatus::VENDU,
            ProductStatus::CHEZ_REVENDEUR,
        ])) {
            return false;
        }

        return $user->can('products.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return $user->can('products.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        // Seul l'admin peut supprimer définitivement
        return $user->isAdmin() && $product->status !== ProductStatus::VENDU;
    }

    /**
     * Determine whether the user can sell the product.
     */
    public function sell(User $user, Product $product): bool
    {
        // Doit avoir la permission de créer des ventes
        if (!$user->can('sales.create')) {
            return false;
        }

        // Le produit doit être disponible à la vente
        return $product->isAvailable();
    }

    /**
     * Determine whether the user can change product status.
     */
    public function changeStatus(User $user, Product $product): bool
    {
        // Les vendeurs ne peuvent pas changer le statut directement
        if ($user->isVendeur()) {
            return false;
        }

        return $user->can('stock.adjustment');
    }

    /**
     * Determine whether the user can update prices.
     */
    public function updatePrices(User $user, Product $product): bool
    {
        // Seul l'admin peut modifier les prix
        return $user->isAdmin();
    }
}
