<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Initialise le composant avec les informations de l'utilisateur.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Met à jour les informations de profil de l'utilisateur connecté.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Envoie un email de vérification à l'utilisateur.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
};
?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informations du profil
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Mettez à jour les informations de votre compte et votre adresse e-mail.
        </p>
    </header>

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">

        <!-- Nom -->
        <div>
            <x-input-label for="name" value="Nom" />
            <x-text-input
                wire:model="name"
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input
                wire:model="email"
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        Votre adresse e-mail n’est pas vérifiée.

                        <button wire:click.prevent="sendVerification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cliquez ici pour renvoyer l’e-mail de vérification.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Bouton enregistrer -->
        <div class="flex items-center gap-4">
            <x-primary-button>Enregistrer</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                Enregistré.
            </x-action-message>
        </div>

    </form>
</section>
