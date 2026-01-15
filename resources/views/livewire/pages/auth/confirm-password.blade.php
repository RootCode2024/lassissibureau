<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $password = '';

    /**
     * Confirme le mot de passe de l'utilisateur actuel.
     */
    public function confirmPassword(): void
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('web')->validate([
            'email' => Auth::user()->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => 'Le mot de passe est incorrect.',
            ]);
        }

        session(['auth.password_confirmed_at' => time()]);

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
};
?>

<div>
    <div class="mb-4 text-sm text-gray-600">
        Il s'agit d'une zone sécurisée de l'application. Veuillez confirmer votre mot de passe avant de continuer.
    </div>

    <form wire:submit="confirmPassword">

        <!-- Mot de passe -->
        <div>
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input
                wire:model="password"
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Bouton confirmer -->
        <div class="flex justify-end mt-4">
            <x-primary-button>
                Confirmer
            </x-primary-button>
        </div>

    </form>
</div>
