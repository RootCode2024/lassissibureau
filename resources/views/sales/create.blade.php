<x-app-layout>
    <x-slot name="header">
        Nouvelle vente
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <x-alerts.error :message="session('error')" />

    <div class="max-w-5xl mx-auto">
        <form method="POST" action="{{ route('sales.store') }}" class="space-y-6">
            @csrf

            {{-- Sélection du produit --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Produit à vendre</h3>
                        <p class="text-xs text-gray-500">Sélectionnez le produit</p>
                    </div>
                </div>

                @if($product)
                    {{-- Produit présélectionné --}}
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="prix_vente" value="{{ $product->prix_vente }}">
                    <input type="hidden" name="prix_achat_produit" value="{{ $product->prix_achat }}">

                    <div class="flex items-start gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200">
                            @php
                                $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                $icon = $categoryIcons[$product->productModel->category] ?? 'box';
                            @endphp
                            <i data-lucide="{{ $icon }}" class="w-8 h-8 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-bold text-gray-900">{{ $product->productModel->name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $product->productModel->brand }}</p>

                            @if($product->imei)
                                <p class="text-xs font-mono text-gray-500 mt-2">IMEI: {{ $product->imei }}</p>
                            @endif

                            <div class="mt-3">
                                <span class="text-sm font-medium text-gray-700">Prix de vente: </span>
                                <span class="text-lg font-bold text-gray-900">{{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Sélection du produit --}}
                    <div>
                        <label for="product_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Produit *
                        </label>
                        <select name="product_id" id="product_id" required class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Sélectionner un produit</option>
                            @foreach($availableProducts as $prod)
                                <option value="{{ $prod->id }}" data-prix="{{ $prod->prix_vente }}" data-achat="{{ $prod->prix_achat }}">
                                    {{ $prod->productModel->name }} - {{ $prod->productModel->brand }}
                                    @if($prod->imei) ({{ $prod->imei }}) @endif
                                    - {{ number_format($prod->prix_vente, 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                    </div>

                    <input type="hidden" name="prix_vente" id="prix_vente" value="{{ old('prix_vente') }}">
                    <input type="hidden" name="prix_achat_produit" id="prix_achat_produit" value="{{ old('prix_achat_produit') }}">
                @endif
            </div>

            {{-- Type de vente --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Type de vente</h3>
                        <p class="text-xs text-gray-500">Vente directe ou troc</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                        <input type="radio" name="sale_type" value="achat_direct" checked required class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Achat direct</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Paiement intégral en espèces</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                        <input type="radio" name="sale_type" value="troc" required class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Troc avec reprise</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Reprise d'ancien appareil + complément espèces</span>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('sale_type')" class="mt-2" />
            </div>

            {{-- Type de client (Direct ou Revendeur) --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Type d'acheteur</h3>
                        <p class="text-xs text-gray-500">Client direct ou revendeur</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                        <input type="radio" name="buyer_type" value="direct" checked class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Client direct</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Vente immédiate confirmée</span>
                        </div>
                    </label>

                    <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                        <input type="radio" name="buyer_type" value="reseller" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                        <div class="ml-3">
                            <span class="block text-sm font-medium text-gray-900">Revendeur</span>
                            <span class="block text-xs text-gray-500 mt-0.5">Dépôt en attente de confirmation</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Section Revendeur (cachée par défaut) --}}
            <div id="reseller-section" class="hidden bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="store" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations revendeur</h3>
                        <p class="text-xs text-gray-500">Sélectionnez le revendeur</p>
                    </div>
                </div>

                <div>
                    <label for="reseller_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Revendeur
                    </label>
                    <select name="reseller_id" id="reseller_id" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Sélectionner un revendeur</option>
                        @foreach($resellers as $reseller)
                            <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                        @endforeach
                    </select>
                </div>

                <input type="hidden" name="date_depot_revendeur" id="date_depot_revendeur" value="{{ now()->format('Y-m-d') }}">
            </div>

            {{-- Informations client --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations client</h3>
                        <p class="text-xs text-gray-500">Coordonnées (optionnel)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="client_name" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Nom du client
                        </label>
                        <input type="text" name="client_name" id="client_name" value="{{ old('client_name') }}" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Nom complet">
                        <x-input-error :messages="$errors->get('client_name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="client_phone" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Téléphone
                        </label>
                        <input type="tel" name="client_phone" id="client_phone" value="{{ old('client_phone') }}" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="+229 XX XX XX XX">
                        <x-input-error :messages="$errors->get('client_phone')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Section Troc (cachée par défaut) --}}
            <div id="troc-section" class="hidden bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="repeat" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations de reprise</h3>
                        <p class="text-xs text-gray-500">Détails de l'appareil repris + complément espèces</p>
                    </div>
                </div>

                <input type="hidden" name="has_trade_in" id="has_trade_in" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="trade_in_modele_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Modèle reçu *
                        </label>
                        <input type="text" name="trade_in[modele_recu]" id="trade_in_modele_recu" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Ex: iPhone 12 64GB">
                    </div>

                    <div>
                        <label for="trade_in_imei_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            IMEI reçu *
                        </label>
                        <input type="text" name="trade_in[imei_recu]" id="trade_in_imei_recu" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-mono" maxlength="15" placeholder="123456789012345">
                    </div>

                    <div class="md:col-span-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-3">Calcul du complément</p>
                        <div class="grid grid-cols-2 gap-4 mb-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Prix de vente</label>
                                <p id="prix_vente_display" class="text-lg font-bold text-gray-900">0 FCFA</p>
                            </div>
                            <div>
                                <label for="trade_in_valeur_reprise" class="block text-xs text-gray-500 mb-1">Valeur de reprise *</label>
                                <div class="relative">
                                    <input type="number" name="trade_in[valeur_reprise]" id="trade_in_valeur_reprise" class="block w-full py-2 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-semibold" min="0" placeholder="0">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-300">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Complément espèces à recevoir</span>
                                <span id="complement_calculated" class="text-xl font-bold text-green-600">0 FCFA</span>
                            </div>
                        </div>
                        <input type="hidden" name="trade_in[complement_especes]" id="trade_in_complement_especes" value="0">
                    </div>

                    <div class="md:col-span-2">
                        <label for="trade_in_etat_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            État du produit reçu
                        </label>
                        <textarea name="trade_in[etat_recu]" id="trade_in_etat_recu" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Décrivez l'état général de l'appareil repris..."></textarea>
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
                        <p class="text-xs text-gray-500">Informations complémentaires (optionnel)</p>
                    </div>
                </div>

                <textarea name="notes" id="notes" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Notes sur la vente...">{{ old('notes') }}</textarea>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="date_vente_effective" value="{{ now()->format('Y-m-d') }}">
            <input type="hidden" name="is_confirmed" id="is_confirmed" value="1">

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
                <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 border border-green-600 rounded-md font-medium text-sm text-white hover:bg-green-700 transition-colors">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Enregistrer la vente
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saleTypeRadios = document.querySelectorAll('input[name="sale_type"]');
            const buyerTypeRadios = document.querySelectorAll('input[name="buyer_type"]');
            const trocSection = document.getElementById('troc-section');
            const resellerSection = document.getElementById('reseller-section');
            const hasTradeInInput = document.getElementById('has_trade_in');
            const isConfirmedInput = document.getElementById('is_confirmed');
            const productSelect = document.getElementById('product_id');
            const prixVenteInput = document.getElementById('prix_vente');
            const prixAchatInput = document.getElementById('prix_achat_produit');
            const valeurRepriseInput = document.getElementById('trade_in_valeur_reprise');
            const complementCalculated = document.getElementById('complement_calculated');
            const complementHiddenInput = document.getElementById('trade_in_complement_especes');
            const prixVenteDisplay = document.getElementById('prix_vente_display');

            // Toggle troc section
            saleTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'achat_direct') {
                        trocSection.classList.add('hidden');
                        hasTradeInInput.value = '0';
                    } else {
                        trocSection.classList.remove('hidden');
                        hasTradeInInput.value = '1';
                        updatePrixVenteDisplay();
                        calculateComplement();
                    }
                });
            });

            // Toggle reseller section
            buyerTypeRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'direct') {
                        resellerSection.classList.add('hidden');
                        isConfirmedInput.value = '1';
                    } else {
                        resellerSection.classList.remove('hidden');
                        isConfirmedInput.value = '0';
                    }
                });
            });

            // Product selection
            if (productSelect) {
                productSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    if (selected.value) {
                        prixVenteInput.value = selected.dataset.prix;
                        prixAchatInput.value = selected.dataset.achat;
                        updatePrixVenteDisplay();
                        calculateComplement();
                    }
                });
            }

            // Calculate complement
            if (valeurRepriseInput) {
                valeurRepriseInput.addEventListener('input', calculateComplement);
            }

            function updatePrixVenteDisplay() {
                const prix = parseFloat(prixVenteInput?.value) || 0;
                if (prixVenteDisplay) {
                    prixVenteDisplay.textContent = prix.toLocaleString('fr-FR') + ' FCFA';
                }
            }

            function calculateComplement() {
                const prixVente = parseFloat(prixVenteInput?.value) || 0;
                const valeurReprise = parseFloat(valeurRepriseInput?.value) || 0;
                const complement = prixVente - valeurReprise;

                if (complementCalculated && complementHiddenInput) {
                    complementCalculated.textContent = complement.toLocaleString('fr-FR') + ' FCFA';
                    complementHiddenInput.value = complement;

                    // Color code
                    if (complement > 0) {
                        complementCalculated.classList.remove('text-gray-900', 'text-red-600');
                        complementCalculated.classList.add('text-green-600');
                    } else if (complement < 0) {
                        complementCalculated.classList.remove('text-gray-900', 'text-green-600');
                        complementCalculated.classList.add('text-red-600');
                    } else {
                        complementCalculated.classList.remove('text-green-600', 'text-red-600');
                        complementCalculated.classList.add('text-gray-900');
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
