<div>
    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6">
        {{-- Informations principales --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations</h3>
                    <p class="text-xs text-gray-500">Coordonnées du revendeur</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Nom du revendeur *
                    </label>
                    <input type="text" wire:model="name" id="name" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Nom complet">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Téléphone *
                    </label>
                    <input type="tel" wire:model="phone" id="phone" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="+229 XX XX XX XX">
                    @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="is_active" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Statut
                    </label>
                    <select wire:model="is_active" id="is_active" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="1">Actif</option>
                        <option value="0">Inactif</option>
                    </select>
                    @error('is_active') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="address" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Adresse
                    </label>
                    <input type="text" wire:model="address" id="address" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Adresse complète">
                    @error('address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Notes</h3>
                    <p class="text-xs text-gray-500">Informations complémentaires</p>
                </div>
            </div>

            <textarea wire:model="notes" id="notes" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Notes internes..."></textarea>
            @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
            <a href="{{ route('resellers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
                Annuler
            </a>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-6 py-2 bg-gray-900 border border-gray-900 rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <span wire:loading wire:target="submit">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="submit">Créer le revendeur</span>
                <span wire:loading wire:target="submit">Création...</span>
            </button>
        </div>
    </form>
</div>
