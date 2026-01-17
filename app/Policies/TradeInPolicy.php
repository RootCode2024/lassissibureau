<?php

namespace App\Policies;

use App\Models\TradeIn;
use App\Models\User;

class TradeInPolicy
{
    /**
     * Voir la liste des trocs
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Voir un troc spécifique
     */
    public function view(User $user, TradeIn $tradeIn): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Créer/traiter un troc (créer le produit)
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Mettre à jour un troc
     */
    public function update(User $user, TradeIn $tradeIn): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Supprimer un troc
     */
    public function delete(User $user, TradeIn $tradeIn): bool
    {
        return $user->hasRole('admin');
    }
}
