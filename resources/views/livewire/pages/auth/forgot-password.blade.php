<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Envoie un lien de réinitialisation du mot de passe à l'adresse e-mail.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // On envoie le lien de réinitialisation à l'utilisateur
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
};
?>

<div class="w-full">

    <!-- Explication -->
    <div class="mb-4 text-sm text-gray-600">
        Mot de passe oublié ? Pas de problème.
        Indiquez simplement votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
    </div>

    <!-- Statut de session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="sendPasswordResetLink" class="space-y-5">

        <!-- Adresse e-mail -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />

            <x-text-input
                wire:model="email"
                id="email"
                type="email"
                class="block mt-1 w-full"
                required
                autofocus
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-1"
            />
        </div>

        <!-- Bouton -->
        <div class="flex items-center justify-end pt-4">
            <x-primary-button class="w-full">
                Envoyer le lien de réinitialisation
            </x-primary-button>
        </div>

    </form>
</div>
