<div>
    {{-- ÉTAPE 1 : Sélection de la catégorie --}}
    @if($step === 1)
        <div class="max-w-5xl mx-auto py-12">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Choisir une catégorie</h2>
                <p class="text-gray-600">Sélectionnez une catégorie pour afficher les produits</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($categories as $category)
                    <button
                        wire:click="selectCategory('{{ $category['value'] }}')"
                        class="group bg-white border-2 border-gray-200 hover:border-black rounded-xl p-8 transition-all duration-200 hover:shadow-lg"
                    >
                        <div class="flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-gray-100 group-hover:bg-black rounded-xl flex items-center justify-center mb-4 transition-colors">
                                <i data-lucide="{{ $category['icon'] }}" class="w-8 h-8 text-gray-600 group-hover:text-white transition-colors"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $category['label'] }}</h3>
                            <div class="flex items-center gap-3 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="layers" class="w-4 h-4"></i>
                                    {{ $category['models_count'] }} modèles
                                </span>
                                <span class="flex items-center gap-1">
                                    <i data-lucide="package" class="w-4 h-4"></i>
                                    {{ $category['products_count'] }} produits
                                </span>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500">Aucune catégorie disponible</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- ÉTAPE 2 : Liste des produits --}}
    @if($step === 2)
        {{-- Bouton retour --}}
        <div class="mb-6">
            <button
                wire:click="backToCategories"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            >
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Retour aux catégories
            </button>
        </div>

        {{-- Statistiques --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">En stock</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Disponibles</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['available'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-5 h-5 text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Chez revendeurs</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['chez_revendeur'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">À réparer</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $stats['a_reparer'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="wrench" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white border border-dashed border-gray-300 rounded-lg mb-6 p-6">
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-4">Filtres de recherche</h3>

            <div class="space-y-4">
                {{-- Recherche --}}
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher par IMEI, nom, marque..."
                        class="block w-full pl-10 pr-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                    >
                </div>

                {{-- Grille de filtres --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">État</label>
                        <select wire:model.live="state" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Tous</option>
                            @foreach($states as $stateOption)
                                <option value="{{ $stateOption['value'] }}">{{ $stateOption['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Localisation</label>
                        <select wire:model.live="location" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Toutes</option>
                            @foreach($locations as $locationOption)
                                <option value="{{ $locationOption['value'] }}">{{ $locationOption['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Modèle</label>
                        <select wire:model.live="product_model_id" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Tous</option>
                            @foreach($productModels as $model)
                                <option value="{{ $model->id }}">{{ $model->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Condition</label>
                        <select wire:model.live="condition" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Toutes</option>
                            @foreach($conditions as $conditionOption)
                                <option value="{{ $conditionOption }}">{{ $conditionOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-2">
                    <button wire:click="resetFilters" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-md font-medium text-sm text-gray-700 transition-colors">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Réinitialiser
                    </button>
                    <a href="{{ route('products.export', ['search' => $search, 'state' => $state, 'location' => $location, 'product_model_id' => $product_model_id, 'condition' => $condition]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 border border-black rounded-md font-medium text-sm text-white transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Exporter CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Tableau --}}
        @if($products->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-12">
                <div class="text-center">
                    <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">Aucun produit trouvé</h3>
                    <p class="text-xs text-gray-500 mb-4">Aucun produit ne correspond à vos critères</p>
                    @can('create', App\Models\Product::class)
                        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 text-white text-sm font-medium rounded-md transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Créer un produit
                        </a>
                    @endcan
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Modèle</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">IMEI / Série</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Source</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">État & Loc.</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($products as $product)
                                <tr class="hover:bg-gray-50 transition-colors" wire:key="product-{{ $product->id }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                @php
                                                    $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                                    $icon = $categoryIcons[$product->productModel->category] ?? 'box';
                                                @endphp
                                                <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $product->productModel->name }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    {{ $product->productModel->brand }}
                                                    @if($product->condition)
                                                        • {{ $product->condition }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($product->imei)
                                            <code class="text-xs font-mono text-gray-900 bg-gray-100 px-2 py-1 rounded">{{ $product->imei }}</code>
                                        @else
                                            <span class="text-xs text-gray-400">N/A</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            @if($product->condition === 'troc')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800 w-fit">
                                                    <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i>
                                                    Troc / Échange
                                                </span>
                                            @elseif($product->condition === 'neuf')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 w-fit">
                                                    Neuf
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 w-fit">
                                                    {{ ucfirst($product->condition ?: 'Standard') }}
                                                </span>
                                            @endif

                                            @if($product->fournisseur)
                                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                    <i data-lucide="truck" class="w-3 h-3"></i>
                                                    {{ $product->fournisseur }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-1.5 items-center">
                                            <x-products.state-badge :state="$product->state" />
                                            <x-products.location-badge :location="$product->location" />
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="text-sm font-semibold text-gray-900">{{ number_format($product->prix_vente, 0, ',', ' ') }}</div>
                                        <div class="text-xs text-gray-500">FCFA</div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 rounded-md transition-colors">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                Voir
                                            </a>
                                            @can('update', $product)
                                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 rounded-md transition-colors">
                                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                    Modifier
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
