<div>
    {{-- Alertes --}}
    @if (session()->has('error'))
        <x-alerts.error :message="session('error')" />
    @endif

    {{-- Carte produit --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
        <div class="bg-gray-900 px-4 sm:px-6 py-3 sm:py-4">
            <h3 class="text-xs sm:text-sm font-semibold text-white uppercase tracking-wide">Produit sélectionné</h3>
        </div>
        <div class="p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 sm:gap-6">
                <div class="flex items-start gap-3 sm:gap-4 min-w-0 flex-1">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        @php
                            $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                            $icon = $categoryIcons[$product->productModel->category->value] ?? 'box';
                        @endphp
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 break-words">{{ $product->productModel->name }}</h4>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1 truncate">{{ $product->productModel->brand }}</p>

                        @if($product->imei)
                            <p class="text-xs text-gray-500 font-mono mt-2 bg-gray-50 inline-block px-2 py-1 rounded break-all">
                                IMEI: {{ $product->imei }}
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-2 sm:mt-3">
                            <x-products.state-badge :state="$product->state" />
                            <x-products.location-badge :location="$product->location" />
                        </div>
                    </div>
                </div>

                <div class="text-left sm:text-right flex-shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-100">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Prix de vente</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 break-all">
                        {{ number_format($product->prix_vente, 0, ',', ' ') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">FCFA</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaire Livewire --}}
    <form wire:submit.prevent="submit" class="space-y-4 sm:space-y-6">

        {{-- Type de vente --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="tag" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Type de vente</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Sélectionnez le type de transaction</p>
                </div>
            </div>

            <div class="space-y-2 sm:space-y-3">
                <label class="relative flex items-start p-3 sm:p-4 border-2 rounded-lg cursor-pointer transition-colors @if($sale_type === 'achat_direct') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 active:border-gray-900 @endif">
                    <input type="radio" wire:model.live="sale_type" value="achat_direct" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900 flex-shrink-0">
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <span class="block text-xs sm:text-sm font-medium text-gray-900">Achat direct</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Paiement intégral en espèces</span>
                    </div>
                </label>

                <label class="relative flex items-start p-3 sm:p-4 border-2 rounded-lg cursor-pointer transition-colors @if($sale_type === 'troc') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 active:border-gray-900 @endif">
                    <input type="radio" wire:model.live="sale_type" value="troc" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900 flex-shrink-0">
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <span class="block text-xs sm:text-sm font-medium text-gray-900">Troc avec reprise</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Reprise d'ancien appareil + complément espèces</span>
                    </div>
                </label>
            </div>
            @error('sale_type') <p class="mt-2 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Informations client --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations client</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Coordonnées du client (optionnel)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                <div>
                    <label for="client_name" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Nom du client
                    </label>
                    <input type="text" wire:model="client_name" id="client_name" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="Nom complet">
                    @error('client_name') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="client_phone" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Téléphone du client
                    </label>
                    <input type="tel" wire:model="client_phone" id="client_phone" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="+229 XX XX XX XX">
                    @error('client_phone') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Section Troc --}}
        @if($sale_type === 'troc')
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="repeat" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations de reprise</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Détails de l'appareil repris + complément espèces</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                    <div>
                        <label for="trade_in_modele_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                            Modèle reçu *
                        </label>
                        <input type="text" wire:model="trade_in_modele_recu" id="trade_in_modele_recu" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="Ex: iPhone 12 64GB">
                        @error('trade_in_modele_recu') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="trade_in_imei_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                            IMEI reçu *
                        </label>
                        <input type="text" wire:model="trade_in_imei_recu" id="trade_in_imei_recu" maxlength="15" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm font-mono" placeholder="123456789012345">
                        @error('trade_in_imei_recu') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2 p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-2 sm:mb-3">Calcul du complément</p>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-2 sm:mb-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Prix de vente</label>
                                <p class="text-base sm:text-lg font-bold text-gray-900 break-all">{{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA</p>
                            </div>
                            <div>
                                <label for="trade_in_valeur_reprise" class="block text-xs text-gray-500 mb-1">Valeur de reprise *</label>
                                <div class="relative">
                                    <input type="number" wire:model.live="trade_in_valeur_reprise" id="trade_in_valeur_reprise" min="0" max="{{ $product->prix_vente }}" class="block w-full py-2 pr-14 sm:pr-16 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm font-semibold" placeholder="0">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:pr-3 pointer-events-none">
                                        <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                    </div>
                                </div>
                                @error('trade_in_valeur_reprise') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        <div class="pt-2 sm:pt-3 border-t border-gray-300">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 sm:gap-0">
                                <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Complément espèces à recevoir</span>
                                <span class="text-lg sm:text-xl font-bold break-all @if($complement_especes > 0) text-green-600 @elseif($complement_especes < 0) text-red-600 @else text-gray-900 @endif">
                                    {{ number_format($complement_especes, 0, ',', ' ') }} FCFA
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="trade_in_etat_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                            État du produit reçu
                        </label>
                        <textarea wire:model="trade_in_etat_recu" id="trade_in_etat_recu" rows="3" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="Décrivez l'état général de l'appareil repris..."></textarea>
                        @error('trade_in_etat_recu') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        @endif

        {{-- Notes --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-text" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Notes additionnelles</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Informations complémentaires (optionnel)</p>
                </div>
            </div>

            <textarea wire:model="notes" id="notes" rows="3" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="Notes sur la vente..."></textarea>
            @error('notes') <p class="mt-1 text-xs sm:text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Récapitulatif --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="calculator" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Récapitulatif</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Détails de la transaction</p>
                </div>
            </div>

            <dl class="space-y-2 sm:space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg gap-3">
                    <dt class="text-xs sm:text-sm text-gray-600">Prix de vente</dt>
                    <dd class="text-xs sm:text-sm font-semibold text-gray-900 break-all text-right">{{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA</dd>
                </div>
                @if(auth()->user()->hasRole('admin'))
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg gap-3">
                        <dt class="text-xs sm:text-sm text-green-700">Bénéfice</dt>
                        <dd class="text-xs sm:text-sm font-semibold text-green-600 break-all text-right">+{{ number_format($product->benefice_potentiel, 0, ',', ' ') }} FCFA</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs sm:text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
                Annuler
            </a>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-2 bg-green-600 border border-green-600 rounded-lg font-medium text-xs sm:text-sm text-white hover:bg-green-700 active:bg-green-800 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </span>
                <span wire:loading wire:target="submit">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="submit" class="hidden xs:inline">Confirmer la vente</span>
                <span wire:loading.remove wire:target="submit" class="xs:hidden">Confirmer</span>
                <span wire:loading wire:target="submit">Traitement...</span>
            </button>
        </div>
    </form>
</div>