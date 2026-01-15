<?php

namespace App\Policies;

use App\Models\CustomerReturn;
use App\Models\User;

class CustomerReturnPolicy
{
    /**
     * Determine if the user can view any customer returns.
     */
    public function viewAny(User $user): bool
    {
        // Admins et vendeurs peuvent voir les retours
        return $user->isAdmin() || $user->isVendeur();
    }

    /**
     * Determine if the user can view the customer return.
     */
    public function view(User $user, CustomerReturn $customerReturn): bool
    {
        return $user->isAdmin() || $user->isVendeur();
    }

    /**
     * Determine if the user can create customer returns.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isVendeur();
    }

    /**
     * Determine if the user can update the customer return.
     */
    public function update(User $user, CustomerReturn $customerReturn): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the customer return.
     */
    public function delete(User $user, CustomerReturn $customerReturn): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can process returned products.
     */
    public function process(User $user, CustomerReturn $customerReturn): bool
    {
        return $user->isAdmin() || $user->isVendeur();
    }
}
