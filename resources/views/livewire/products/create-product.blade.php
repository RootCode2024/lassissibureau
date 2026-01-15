<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900">Créer un produit</h2>
                <p class="text-sm text-gray-500 mt-1">Ajoutez un ou plusieurs produits à votre inventaire</p>
            </div>
        </div>
    </x-slot>

    <div class="w-full px-4 sm:px-6 lg:px-8">
        <form wire:submit="save" class="space-y-6">

            {{-- Layout en 2 colonnes : Formulaire à gauche, IMEI à droite --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Colonne gauche : Informations du produit --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Sélection du modèle --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                <i data-lucide="box" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations du modèle</h3>
                                <p class="text-xs text-gray-500">Sélectionnez le modèle de produit</p>
                            </div>
                        </div>

                        <div>
                            <label for="product_model_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Modèle de produit *
                            </label>
                            <select wire:model.live="product_model_id" id="product_model_id" required
                                    class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                <option value="">Sélectionner un modèle</option>
                                @foreach($this->productModels as $model)
                                    <option value="{{ $model->id }}">
                                        {{ $model->name }} - {{ $model->brand }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_model_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($this->selectedModel)
                            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                                    <div class="text-xs text-blue-700">
                                        <strong>Catégorie:</strong> {{ $this->selectedModel->category }}
                                        @if($this->selectedModel->storage)
                                            <span class="ml-2">• <strong>Stockage:</strong> {{ $this->selectedModel->storage }}</span>
                                        @endif
                                        @if($this->selectedModel->color)
                                            <span class="ml-2">• <strong>Couleur:</strong> {{ $this->selectedModel->color }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- État et Localisation --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">État & Localisation</h3>
                                <p class="text-xs text-gray-500">Statut actuel des produits</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="state" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    État *
                                </label>
                                <select wire:model="state" id="state" required
                                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                    @foreach($states as $state)
                                        <option value="{{ $state['value'] }}">{{ $state['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('state')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    Localisation *
                                </label>
                                <select wire:model="location" id="location" required
                                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                    @foreach($locations as $location)
                                        <option value="{{ $location['value'] }}">{{ $location['label'] }}</option>
                                    @endforeach
                                </select>
                                @error('location')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Prix --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                <i data-lucide="coins" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Tarification</h3>
                                <p class="text-xs text-gray-500">Prix d'achat et de vente</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="prix_achat" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    Prix d'achat (FCFA) *
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        wire:model.live="prix_achat"
                                        id="prix_achat"
                                        class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                        min="0"
                                        step="1"
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                    </div>
                                </div>
                                @error('prix_achat')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="prix_vente" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    Prix de vente (FCFA) *
                                </label>
                                <div class="relative">
                                    <input
                                        type="number"
                                        wire:model.live="prix_vente"
                                        id="prix_vente"
                                        class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                        min="0"
                                        step="1"
                                        required
                                    />
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                    </div>
                                </div>
                                @error('prix_vente')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1.5 text-xs text-gray-500">Doit être supérieur ou égal au prix d'achat</p>
                            </div>
                        </div>

                        {{-- Aperçu de la marge --}}
                        @if($prix_achat && $prix_vente && $prix_achat > 0 && $prix_vente > 0)
                            @php
                                $benefice = $prix_vente - $prix_achat;
                                $marge = (($benefice / $prix_vente) * 100);
                                $roi = (($benefice / $prix_achat) * 100);

                                $margeColor = $marge >= 30 ? 'text-green-600' : ($marge >= 15 ? 'text-yellow-600' : 'text-gray-900');
                            @endphp

                            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                <div class="grid grid-cols-3 gap-4 text-center">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Bénéfice</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($benefice, 0, ',', ' ') }} FCFA</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Marge</p>
                                        <p class="text-lg font-bold {{ $margeColor }}">{{ number_format($marge, 2) }}%</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">ROI</p>
                                        <p class="text-lg font-bold text-gray-900">{{ number_format($roi, 2) }}%</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Détails complémentaires --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                <i data-lucide="info" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Détails complémentaires</h3>
                                <p class="text-xs text-gray-500">Condition et informations d'achat</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="condition" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    Condition
                                </label>
                                <select wire:model="condition" id="condition"
                                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                    <option value="">Sélectionner une condition</option>
                                    @foreach($conditions as $cond)
                                        <option value="{{ $cond }}">{{ $cond }}</option>
                                    @endforeach
                                </select>
                                @error('condition')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="date_achat" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                    Date d'achat
                                </label>
                                <input
                                    type="date"
                                    wire:model="date_achat"
                                    id="date_achat"
                                    class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                    max="{{ now()->format('Y-m-d') }}"
                                />
                                @error('date_achat')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="fournisseur" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Fournisseur
                            </label>
                            <input
                                type="text"
                                wire:model="fournisseur"
                                id="fournisseur"
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                placeholder="Nom du fournisseur"
                            />
                            @error('fournisseur')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="defauts" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Défauts constatés
                            </label>
                            <textarea
                                wire:model="defauts"
                                id="defauts"
                                rows="3"
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                placeholder="Décrivez les défauts ou problèmes des produits..."
                            ></textarea>
                            @error('defauts')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                                Notes
                            </label>
                            <textarea
                                wire:model="notes"
                                id="notes"
                                rows="3"
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                placeholder="Notes additionnelles..."
                            ></textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Colonne droite : Section IMEI (sticky) --}}
                <div class="lg:col-span-1">
                    <div class="lg:sticky lg:top-6">
                        <div class="bg-white border border-gray-200 rounded-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                                        <i data-lucide="hash" class="w-5 h-5 text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Identifications</h3>
                                        <p class="text-xs text-gray-500">IMEI et numéros de série</p>
                                    </div>
                                </div>
                            </div>

                            <button
                                type="button"
                                wire:click="addProduct"
                                class="w-full mb-4 inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-gray-900 text-white rounded-md text-xs font-medium hover:bg-gray-800 transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Ajouter un produit
                            </button>

                            @error('products')
                                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-center gap-2 text-xs text-red-700">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                </div>
                            @enderror

                            <div class="space-y-3 max-h-[calc(100vh-16rem)] overflow-y-auto">
                                @foreach($products as $index => $product)
                                    <div class="relative p-3 border border-gray-200 rounded-lg hover:border-gray-300 transition-colors bg-gray-50"
                                         wire:key="product-{{ $product['id'] }}">

                                        {{-- En-tête du produit --}}
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-semibold text-gray-700">Produit #{{ $index + 1 }}</span>
                                            @if(count($products) > 1)
                                                <button
                                                    type="button"
                                                    wire:click="removeProduct({{ $index }})"
                                                    class="w-5 h-5 bg-red-100 hover:bg-red-200 text-red-600 rounded-full flex items-center justify-center transition-colors">
                                                    <i data-lucide="x" class="w-3 h-3"></i>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- IMEI --}}
                                        <div class="mb-3">
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                IMEI
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="products.{{ $index }}.imei"
                                                class="block w-full py-2 px-3 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-xs font-mono bg-white"
                                                maxlength="15"
                                                placeholder="123456789012345"
                                            />
                                            @error("products.{$index}.imei")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Numéro de série --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1.5">
                                                N° de série
                                            </label>
                                            <input
                                                type="text"
                                                wire:model="products.{{ $index }}.serial_number"
                                                class="block w-full py-2 px-3 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-xs font-mono bg-white"
                                                placeholder="SN123456789"
                                            />
                                            @error("products.{$index}.serial_number")
                                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if(count($products) > 1)
                                <div class="mt-4 p-2.5 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center gap-2 text-xs text-green-700">
                                        <i data-lucide="package-check" class="w-4 h-4"></i>
                                        <span><strong>{{ count($products) }}</strong> produits à créer</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Annuler
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-2 bg-black border border-black rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove>
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </span>
                    <span wire:loading>
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    </span>
                    <span wire:loading.remove>
                        {{ count($products) > 1 ? 'Créer les ' . count($products) . ' produits' : 'Créer le produit' }}
                    </span>
                    <span wire:loading>Création en cours...</span>
                </button>
            </div>
        </form>
    </div>
</div>
