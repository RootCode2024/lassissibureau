<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Traite la tentative de connexion.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(
            default: route('dashboard', absolute: false),
            navigate: true
        );
    }
};
?>

<div class="w-full">

    <!-- Titre -->
    <h1 class="text-2xl font-semibold text-gray-900 mb-2">
        Connexion
    </h1>

    <p class="text-sm text-gray-600 mb-6">
        Accédez à votre espace de gestion.
    </p>

    <!-- Statut session -->
    <x-auth-session-status
        class="mb-4"
        :status="session('status')"
    />

    <form wire:submit="login" class="space-y-5">

        <!-- Email -->
        <div>
            <x-input-label
                for="email"
                value="Adresse e-mail"
            />

            <x-text-input
                wire:model="form.email"
                id="email"
                type="email"
                class="block mt-1 w-full"
                required
                autofocus
                autocomplete="username"
            />

            <x-input-error
                :messages="$errors->get('form.email')"
                class="mt-1"
            />
        </div>

        <!-- Mot de passe -->
        <div>
            <x-input-label
                for="password"
                value="Mot de passe"
            />

            <x-text-input
                wire:model="form.password"
                id="password"
                type="password"
                class="block mt-1 w-full"
                required
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->get('form.password')"
                class="mt-1"
            />
        </div>

        <!-- Se souvenir de moi -->
        <div class="flex items-center justify-between">

            <label for="remember" class="flex items-center">
                <input
                    wire:model="form.remember"
                    id="remember"
                    type="checkbox"
                    class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                >
                <span class="ml-2 text-sm text-gray-700">
                    Se souvenir de moi
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    wire:navigate
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Mot de passe oublié ?
                </a>
            @endif
        </div>

        <!-- Bouton -->
        <div class="pt-4">
            <button
                type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-2
                       bg-gray-900 border border-transparent rounded-md
                       text-sm font-medium text-white
                       hover:bg-gray-800 focus:outline-none focus:ring-2
                       focus:ring-offset-2 focus:ring-gray-900"
            >
                Se connecter
            </button>
        </div>

    </form>
</div>
