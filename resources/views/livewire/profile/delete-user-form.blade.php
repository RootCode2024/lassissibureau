<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Supprime l'utilisateur actuellement connecté.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
};
?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Supprimer le compte
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Une fois votre compte supprimé, toutes vos données et ressources seront définitivement supprimées.
            Avant de supprimer votre compte, veuillez télécharger toute information que vous souhaitez conserver.
        </p>
    </header>

    <!-- Bouton supprimer -->
    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        Supprimer le compte
    </x-danger-button>

    <!-- Modal de confirmation -->
    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">

            <h2 class="text-lg font-medium text-gray-900">
                Êtes-vous sûr de vouloir supprimer votre compte ?
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Une fois votre compte supprimé, toutes vos données et ressources seront définitivement supprimées.
                Veuillez entrer votre mot de passe pour confirmer la suppression permanente de votre compte.
            </p>

            <!-- Mot de passe -->
            <div class="mt-6">
                <x-input-label for="password" value="Mot de passe" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="Mot de passe"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Annuler
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    Supprimer le compte
                </x-danger-button>
            </div>

        </form>
    </x-modal>
</section>
