<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900">Nouvel utilisateur</h2>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('users.store') }}" class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 space-y-4 sm:space-y-6 shadow-sm">
            @csrf
            
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-1">Informations du compte</h3>
                <p class="text-xs sm:text-sm text-gray-500">Créez un nouvel accès pour un administrateur ou un vendeur.</p>
            </div>

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                    Nom complet <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name') }}" 
                    required 
                    autofocus 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                    placeholder="Ex: Jean Dupont"
                >
                @error('name')
                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                    Email professionnel <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    required 
                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                    placeholder="exemple@entreprise.com"
                >
                @error('email')
                    <p class="mt-1.5 text-xs sm:text-sm text-red-600 flex items-center gap-1">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Rôle (Automatique) --}}
            <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">Rôle</label>
                <div class="px-3 sm:px-4 py-2 sm:py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 text-sm font-medium flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4 text-gray-500"></i>
                    Vendeur
                </div>
                <p class="mt-2 text-xs text-gray-500 flex items-start gap-1.5">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                    <span>Ce compte aura les accès limités d'un vendeur (Ventes, Clients, Stock en lecture).</span>
                </p>
            </div>

            <div class="border-t border-gray-200 pt-4 sm:pt-6"></div>

            {{-- Mot de passe --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                        Mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autocomplete="new-password" 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1.5 text-xs sm:text-sm text-red-600 flex items-center gap-1">
                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                        Confirmer mot de passe <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"
                        placeholder="••••••••"
                    >
                </div>
            </div>

            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center sm:justify-end gap-2 sm:gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('users.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 active:bg-gray-950 transition-all hover:shadow-lg">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Créer l'utilisateur</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>