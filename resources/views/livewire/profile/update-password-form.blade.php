<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Met à jour le mot de passe de l'utilisateur connecté.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
};
?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Changer le mot de passe
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Assurez-vous que votre compte utilise un mot de passe long et sécurisé.
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">

        <!-- Mot de passe actuel -->
        <div>
            <x-input-label for="update_password_current_password" value="Mot de passe actuel" />
            <x-text-input
                wire:model="current_password"
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
        </div>

        <!-- Nouveau mot de passe -->
        <div>
            <x-input-label for="update_password_password" value="Nouveau mot de passe" />
            <x-text-input
                wire:model="password"
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmation du mot de passe -->
        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmer le mot de passe" />
            <x-text-input
                wire:model="password_confirmation"
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Bouton enregistrer -->
        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>

            <x-action-message class="me-3" on="password-updated">
                Enregistré.
            </x-action-message>
        </div>

    </form>
</section>
