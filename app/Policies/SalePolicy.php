<?php

namespace App\Policies;

use App\Models\Sale;
use App\Models\User;

class SalePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('sales.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Sale $sale): bool
    {
        // Les vendeurs peuvent voir leurs propres ventes
        if ($user->isVendeur() && $sale->sold_by === $user->id) {
            return true;
        }

        return $user->can('sales.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('sales.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Sale $sale): bool
    {
        // Ne peut pas modifier une vente confirmée
        if ($sale->is_confirmed) {
            return false;
        }

        // Les vendeurs ne peuvent modifier que leurs propres ventes
        if ($user->isVendeur()) {
            return $sale->sold_by === $user->id && $user->can('sales.edit');
        }

        return $user->can('sales.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Sale $sale): bool
    {
        // Ne peut pas supprimer une vente confirmée
        if ($sale->is_confirmed) {
            return false;
        }

        // Seul l'admin peut supprimer des ventes
        return $user->isAdmin() && $user->can('sales.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Sale $sale): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Sale $sale): bool
    {
        return $user->isAdmin() && !$sale->is_confirmed;
    }

    /**
     * Determine whether the user can confirm a sale (for reseller sales).
     */
    public function confirm(User $user, Sale $sale): bool
    {
        // Seul l'admin peut confirmer les ventes revendeurs
        if (!$user->isAdmin()) {
            return false;
        }

        // La vente doit être via un revendeur et non confirmée
        return $sale->reseller_id !== null && !$sale->is_confirmed;
    }

    /**
     * Determine whether the user can view sale details (including profit).
     */
    public function viewProfit(User $user, Sale $sale): bool
    {
        // Les vendeurs peuvent voir le profit de leurs propres ventes
        if ($user->isVendeur() && $sale->sold_by === $user->id) {
            return true;
        }

        return $user->can('reports.view');
    }

    /**
     * Determine whether the user can export sales.
     */
    public function export(User $user): bool
    {
        return $user->can('reports.export');
    }

    /**
     * Determine whether the user can process returns.
     */
    public function processReturn(User $user, Sale $sale): bool
    {
        // La vente doit être confirmée
        if (!$sale->is_confirmed) {
            return false;
        }

        return $user->can('returns.manage');
    }
}
