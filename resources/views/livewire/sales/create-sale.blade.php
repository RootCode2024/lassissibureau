
        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">{{ session('error') }}</p>
            </div>
        @endif

        <div class="max-w-5xl mx-auto">
            <form wire:submit="save" class="space-y-6">
                {{-- Sélection du produit --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="package" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Quel produit vend-on ?</h3>
                            <p class="text-xs text-gray-500">Choisissez le téléphone ou l'accessoire</p>
                        </div>
                    </div>

                    @if($preselectedProduct)
                        {{-- Produit présélectionné --}}
                        <div class="flex items-start gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center flex-shrink-0 border border-gray-200">
                                @php
                                    $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                    $icon = $categoryIcons[$preselectedProduct->productModel->category] ?? 'box';
                                @endphp
                                <i data-lucide="{{ $icon }}" class="w-8 h-8 text-gray-600"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-gray-900">{{ $preselectedProduct->productModel->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $preselectedProduct->productModel->brand }}</p>

                                @if($preselectedProduct->imei)
                                    <p class="text-xs font-mono text-gray-500 mt-2">IMEI: {{ $preselectedProduct->imei }}</p>
                                @endif

                                <div class="mt-3">
                                    <span class="text-sm font-medium text-gray-700">Prix de vente: </span>
                                    <span class="text-lg font-bold text-gray-900">{{ number_format($preselectedProduct->prix_vente, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Sélection du produit --}}
                        <div>
                            <label for="product_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Produit *
                            </label>
                            <select wire:model.live="product_id" id="product_id" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                <option value="">Sélectionner un produit</option>
                                @foreach($availableProducts as $prod)
                                    <option value="{{ $prod->id }}">
                                        {{ $prod->productModel->name }} - {{ $prod->productModel->brand }}
                                        @if($prod->imei) ({{ $prod->imei }}) @endif
                                        - {{ number_format($prod->prix_vente, 0, ',', ' ') }} FCFA
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                {{-- Type de vente --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Type de Vente</h3>
                            <p class="text-xs text-gray-500">Vente simple ou avec échange</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input wire:model.live="sale_type" type="radio" value="achat_direct" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Vente Simple</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Le client paie et part avec le produit</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input wire:model.live="sale_type" type="radio" value="troc" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Échange / Troc</span>
                                <span class="block text-xs text-gray-500 mt-0.5">On reprend son ancien téléphone + de l'argent</span>
                            </div>
                        </label>
                    </div>
                    @error('sale_type') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                {{-- Type d'acheteur --}}
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="users" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Qui est le client ?</h3>
                            <p class="text-xs text-gray-500">Client de passage ou Revendeur</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input wire:model.live="buyer_type" type="radio" value="direct" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Client Boutique</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Un client normal qui vient acheter</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                            <input wire:model.live="buyer_type" type="radio" value="reseller" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Revendeur / Partenaire</span>
                                <span class="block text-xs text-gray-500 mt-0.5">Un revendeur qui prend pour revendre</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Section Revendeur --}}
                @if($buyer_type === 'reseller')
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="store" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Choix du Revendeur</h3>
                            <p class="text-xs text-gray-500">Qui prend le produit ?</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label for="reseller_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Revendeur *
                            </label>
                            <select wire:model="reseller_id" id="reseller_id" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                <option value="">Sélectionner un revendeur</option>
                                @foreach($resellers as $reseller)
                                    <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                                @endforeach
                            </select>
                            @error('reseller_id') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                            @error('reseller_id') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Date de dépôt --}}
                        <div>
                            <label for="date_depot_revendeur" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Date du dépôt
                            </label>
                            <input wire:model="date_depot_revendeur" type="date" id="date_depot_revendeur" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            @error('date_depot_revendeur') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Confirmation immédiate (Nouveau) --}}
                        <div class="relative flex items-start p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center h-5">
                                <input wire:model="reseller_confirm_immediate" id="reseller_confirm_immediate" type="checkbox" class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="reseller_confirm_immediate" class="font-medium text-blue-900">Vente directe (Pas de dépôt)</label>
                                <p class="text-blue-700">Cochez cette case si le revendeur <strong>achète</strong> le téléphone maintenant (Vente confirmée).<br>Laissez vide si c'est un simple <strong>dépôt</strong>.</p>
                            </div>
                        </div>

                        {{-- Options de paiement --}}
                        <div class="pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Modalités de paiement</h4>
                            <div class="space-y-3">
                                <label class="relative flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                                    <input wire:model.live="payment_option" type="radio" value="unpaid" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Crédit Total (Paiera plus tard)</span>
                                        <span class="block text-xs text-gray-500 mt-0.5">Il ne verse rien aujourd'hui</span>
                                    </div>
                                </label>

                                <label class="relative flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-gray-900 transition-colors has-[:checked]:border-gray-900 has-[:checked]:bg-gray-50">
                                    <input wire:model.live="payment_option" type="radio" value="partial" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                                    <div class="ml-3">
                                        <span class="block text-sm font-medium text-gray-900">Avance (Paiement Partiel)</span>
                                        <span class="block text-xs text-gray-500 mt-0.5">Il verse une partie aujourd'hui</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Montant payé (si paiement partiel) --}}
                        @if($payment_option === 'partial')
                        <div>
                            <label for="amount_paid" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Montant payé maintenant *
                            </label>
                            <div class="relative">
                                <input wire:model="amount_paid" type="number" id="amount_paid" min="0" step="0.01" class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="0">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                </div>
                            </div>
                            @error('amount_paid') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        {{-- Date d'échéance --}}
                        <div>
                            <label for="payment_due_date" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Date d'échéance
                            </label>
                            <input wire:model="payment_due_date" type="date" id="payment_due_date" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            @error('payment_due_date') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        {{-- Méthode de paiement --}}
                        <div>
                            <label for="payment_method" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Méthode de paiement
                            </label>
                            <select wire:model="payment_method" id="payment_method" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                <option value="cash">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Virement bancaire</option>
                                <option value="check">Chèque</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif

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
                            <input wire:model="client_name" type="text" id="client_name" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Nom complet">
                            @error('client_name') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="client_phone" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Téléphone
                            </label>
                            <input wire:model="client_phone" type="tel" id="client_phone" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="+229 XX XX XX XX">
                            @error('client_phone') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- Section Troc --}}
                @if($has_trade_in)
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="repeat" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations de reprise</h3>
                            <p class="text-xs text-gray-500">Détails de l'appareil repris</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="trade_in_modele_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Modèle reçu *
                            </label>
                            <input wire:model="trade_in_modele_recu" type="text" id="trade_in_modele_recu" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Ex: iPhone 12 64GB">
                            @error('trade_in_modele_recu') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="trade_in_imei_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                IMEI reçu *
                            </label>
                            <input wire:model="trade_in_imei_recu" type="text" id="trade_in_imei_recu" maxlength="15" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-mono" placeholder="123456789012345">
                            @error('trade_in_imei_recu') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <p class="text-xs font-medium text-gray-700 uppercase tracking-wide mb-3">Calcul du complément</p>
                            <div class="grid grid-cols-2 gap-4 mb-3">
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Prix de vente</label>
                                    <p class="text-lg font-bold text-gray-900">{{ number_format($prix_vente ?? 0, 0, ',', ' ') }} FCFA</p>
                                </div>
                                <div>
                                    <label for="trade_in_valeur_reprise" class="block text-xs text-gray-500 mb-1">Valeur de reprise *</label>
                                    <div class="relative">
                                        <input wire:model.live="trade_in_valeur_reprise" type="number" id="trade_in_valeur_reprise" min="0" step="0.01" class="block w-full py-2 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-semibold" placeholder="0">
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                        </div>
                                    </div>
                                    @error('trade_in_valeur_reprise') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="pt-3 border-t border-gray-300">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-gray-600 uppercase tracking-wide">Complément espèces à recevoir</span>
                                    <span class="text-xl font-bold {{ $trade_in_complement_especes >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($trade_in_complement_especes, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <label for="trade_in_etat_recu" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                État du produit reçu
                            </label>
                            <textarea wire:model="trade_in_etat_recu" id="trade_in_etat_recu" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Décrivez l'état général de l'appareil repris..."></textarea>
                            @error('trade_in_etat_recu') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                @endif

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

                    <textarea wire:model="notes" id="notes" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Notes sur la vente..."></textarea>
                    @error('notes') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-4 bg-white border border-gray-200 rounded-lg p-6">
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                            <div>
                                <h4 class="text-sm font-semibold text-red-800">Attention, il y a des erreurs dans le formulaire</h4>
                                <ul class="text-xs text-red-600 mt-1 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between gap-4">
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Annuler
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-green-600 border border-green-600 rounded-md font-medium text-sm text-white hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                        </span>
                        <span wire:loading wire:target="save">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="save">Enregistrer la vente</span>
                        <span wire:loading wire:target="save">Enregistrement...</span>
                    </button>
                    </div>
                </div>
            </form>
        </div>
