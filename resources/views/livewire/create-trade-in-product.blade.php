<div>
    @if (session()->has('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <div class="space-y-6">
        {{-- Informations du troc --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i data-lucide="repeat" class="w-5 h-5 text-white"></i>
                </div>
                <h3 class="text-sm font-semibold text-blue-900 uppercase tracking-wide">Informations du troc</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-blue-700 uppercase tracking-wide">Vente associée</p>
                    <p class="text-sm font-medium text-blue-900">Vente #{{ $tradeIn->sale_id }}</p>
                    <p class="text-xs text-blue-600">{{ $tradeIn->created_at->format('d/m/Y à H:i') }}</p>
                </div>

                <div>
                    <p class="text-xs text-blue-700 uppercase tracking-wide">Vendeur</p>
                    <p class="text-sm font-medium text-blue-900">{{ $tradeIn->sale->seller->name }}</p>
                </div>

                <div>
                    <p class="text-xs text-blue-700 uppercase tracking-wide">Modèle reçu</p>
                    <p class="text-sm font-semibold text-blue-900">{{ $tradeIn->modele_recu }}</p>
                </div>

                <div>
                    <p class="text-xs text-blue-700 uppercase tracking-wide">IMEI reçu</p>
                    <p class="text-sm font-mono text-blue-900">{{ $tradeIn->imei_recu }}</p>
                </div>

                @if($tradeIn->etat_recu)
                <div class="md:col-span-2">
                    <p class="text-xs text-blue-700 uppercase tracking-wide">État constaté</p>
                    <p class="text-sm text-blue-800">{{ $tradeIn->etat_recu }}</p>
                </div>
                @endif

                <div class="md:col-span-2 p-4 bg-white rounded-lg">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-blue-700 font-medium uppercase">Valeur de reprise</span>
                        <span class="text-xl font-bold text-blue-900">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Formulaire de création --}}
        <form wire:submit.prevent="submit" class="space-y-6">
            {{-- Sélection du modèle --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="smartphone" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Modèle du produit</h3>
                        <p class="text-xs text-gray-500">Sélectionnez le modèle correspondant</p>
                    </div>
                </div>

                <div>
                    <label for="product_model_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Modèle *
                    </label>
                    <select wire:model="product_model_id" id="product_model_id" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Sélectionner un modèle</option>
                        @foreach($productModels as $model)
                        <option value="{{ $model->id }}">
                            {{ $model->brand }} {{ $model->name }}
                            @if($model->storage) - {{ $model->storage }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('product_model_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Prix --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Prix de vente</h3>
                        <p class="text-xs text-gray-500">Définir le prix de revente</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="marge_percentage" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Marge (%)
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                wire:model.live.debounce.300ms="marge_percentage" 
                                id="marge_percentage" 
                                min="0" 
                                step="1" 
                                class="block w-full py-2.5 pr-10 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" 
                                placeholder="20"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs text-gray-500 font-medium">%</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Prix d'achat : <span class="font-semibold">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</span>
                        </p>
                    </div>

                    <div>
                        <label for="prix_vente" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Prix de vente *
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                wire:model.live.debounce.300ms="prix_vente" 
                                id="prix_vente" 
                                min="0" 
                                step="1000" 
                                class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-semibold" 
                                placeholder="0"
                            >
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs text-gray-500 font-medium">FCFA</span>
                            </div>
                        </div>
                        @if($prix_vente && $tradeIn->valeur_reprise)
                            @php
                                $benefice = $prix_vente - $tradeIn->valeur_reprise;
                            @endphp
                            <p class="mt-1 text-xs {{ $benefice >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                Bénéfice: {{ $benefice >= 0 ? '+' : '' }}{{ number_format($benefice, 0, ',', ' ') }} FCFA
                            </p>
                        @endif
                        @error('prix_vente') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Indicateurs visuels --}}
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Prix d'achat</p>
                            <p class="text-lg font-bold text-gray-900">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }}</p>
                            <p class="text-xs text-gray-500">FCFA</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Marge</p>
                            <p class="text-lg font-bold text-blue-600">{{ number_format($marge_percentage ?? 0, 2) }}%</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Prix de vente</p>
                            <p class="text-lg font-bold text-green-600">{{ number_format($prix_vente ?? 0, 0, ',', ' ') }}</p>
                            <p class="text-xs text-gray-500">FCFA</p>
                        </div>
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

                <textarea wire:model="notes" id="notes" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Notes sur le produit..."></textarea>
                @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
                <a href="{{ route('trade-ins.pending') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Annuler
                </a>
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 border border-blue-600 rounded-md font-medium text-sm text-white hover:bg-blue-700 transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="submit">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading wire:target="submit">
                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="submit">Créer le produit</span>
                    <span wire:loading wire:target="submit">Création...</span>
                </button>
            </div>
        </form>
    </div>
</div>
