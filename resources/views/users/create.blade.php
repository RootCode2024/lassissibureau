<x-app-layout>
    <x-slot name="header">
        Nouvel utilisateur
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('users.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-6">
            @csrf
            
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Informations du compte</h3>
                <p class="text-sm text-gray-500">Créez un nouvel accès pour un administrateur ou un vendeur.</p>
            </div>

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Rôle (Automatique) --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                <div class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-600 sm:text-sm">
                    Vendeur
                </div>
                <p class="mt-2 text-xs text-gray-500">
                    Ce compte aura les accès limités d'un vendeur (Ventes, Clients, Stock en lecture).
                </p>
                {{-- Admin ne peut pas être créé ici --}}
            </div>

            <div class="border-t border-gray-200 pt-6"></div>

            {{-- Mot de passe --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer mot de passe</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('users.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
