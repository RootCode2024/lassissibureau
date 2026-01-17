<div>
    {{-- Statistiques --}}
    <div class="mb-4 sm:mb-6 grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 lg:p-6">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5 sm:mb-1 truncate">Total</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">{{ $stats['total'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="package" class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 lg:p-6">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5 sm:mb-1 truncate">Actifs</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">{{ $stats['actifs'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-4 h-4 lg:w-5 lg:h-5 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 lg:p-6">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5 sm:mb-1 truncate">Stock Faible</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">{{ $stats['stock_faible'] }}</p>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4 lg:w-5 lg:h-5 text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 lg:p-6">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-0.5 sm:mb-1 truncate">Catégories</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 truncate">4</p>
                </div>
                <div class="w-8 h-8 sm:w-9 sm:h-9 lg:w-10 lg:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="grid" class="w-4 h-4 lg:w-5 lg:h-5 text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-gray-200 rounded-lg mb-4 sm:mb-6 p-3 sm:p-4 lg:p-6">
        <h3 class="text-[10px] sm:text-xs font-semibold text-gray-900 uppercase tracking-wide mb-3 sm:mb-4">Filtres</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Recherche --}}
            <div class="sm:col-span-2">
                <label class="block text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                    Recherche
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher..."
                        class="block w-full pl-10 pr-10 py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm transition-colors"
                    >
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            type="button"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center hover:bg-gray-50 rounded-r-lg transition-colors"
                            aria-label="Effacer la recherche"
                        >
                            <i data-lucide="x" class="w-4 h-4 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Catégorie --}}
            <div>
                <label class="block text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                    Catégorie
                </label>
                <select
                    wire:model.live="category"
                    class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm transition-colors"
                >
                    <option value="">Toutes</option>
                    <option value="telephone">📱 Téléphones</option>
                    <option value="tablette">💻 Tablettes</option>
                    <option value="pc">🖥️ Ordinateurs</option>
                    <option value="accessoire">🎧 Accessoires</option>
                </select>
            </div>

            {{-- Bouton reset --}}
            <div class="flex items-end">
                <button
                    wire:click="resetFilters"
                    type="button"
                    class="w-full inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 bg-white hover:bg-gray-50 active:bg-gray-100 border border-gray-300 rounded-lg font-medium text-sm text-gray-700 transition-colors"
                >
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    <span>Réinitialiser</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Contenu --}}
    @if($productModels->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 lg:p-12">
            <div class="flex flex-col items-center gap-3 sm:gap-4 text-center max-w-md mx-auto">
                <div class="w-12 h-12 sm:w-16 sm:h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center">
                    <i data-lucide="inbox" class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400"></i>
                </div>
                <div>
                    <p class="text-sm sm:text-base font-medium text-gray-900">Aucun modèle trouvé</p>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Essayez de modifier vos critères de recherche</p>
                </div>
                @if($search || $category)
                    <button
                        wire:click="resetFilters"
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 active:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Réinitialiser les filtres
                    </button>
                @endif
            </div>
        </div>
    @else
        {{-- Vue Desktop (Tableau) --}}
        <div class="hidden lg:block bg-white border border-gray-200 rounded-lg overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produit</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catégorie</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Stock</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix de vente</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($productModels as $model)
                            <tr class="hover:bg-gray-50 transition-colors" wire:key="model-desktop-{{ $model->id }}">
                                <td class="px-4 xl:px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $iconMap = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                            $icon = $iconMap[$model->category->value] ?? 'box';
                                        @endphp
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $model->name }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5 truncate">{{ $model->brand }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 xl:px-6 py-4">
                                    @php
                                        $categoryLabels = ['telephone' => '📱 Téléphone', 'tablette' => '💻 Tablette', 'pc' => '🖥️ Ordinateur', 'accessoire' => '🎧 Accessoire'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200 whitespace-nowrap">
                                        {{ $categoryLabels[$model->category->value] ?? ucfirst($model->category->value) }}
                                    </span>
                                </td>

                                <td class="px-4 xl:px-6 py-4">
                                    <div class="flex justify-center">
                                        @if($model->products_in_stock_count < $model->stock_minimum)
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 border border-red-200 rounded-lg">
                                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-red-600 flex-shrink-0"></i>
                                                <span class="text-sm font-semibold text-red-700">{{ $model->products_in_stock_count ?? 0 }}</span>
                                            </div>
                                        @else
                                            <div class="inline-flex items-center px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg">
                                                <span class="text-sm font-semibold text-gray-900">{{ $model->products_in_stock_count ?? 0 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 xl:px-6 py-4 text-right">
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($model->prix_vente_default, 0, ',', ' ') }}</div>
                                    <div class="text-xs text-gray-500">FCFA</div>
                                </td>

                                <td class="px-4 xl:px-6 py-4">
                                    <div class="flex justify-center">
                                        @if($model->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white text-gray-900 border border-gray-300">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full flex-shrink-0"></span>
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white text-gray-500 border border-gray-300">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full flex-shrink-0"></span>
                                                Inactif
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 xl:px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('product-models.show', $model) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span class="hidden xl:inline">Voir</span>
                                        </a>
                                        @can('update', $model)
                                            <a href="{{ route('product-models.edit', $model) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                <span class="hidden xl:inline">Modifier</span>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($productModels->hasPages())
                <div class="px-4 xl:px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $productModels->links() }}
                </div>
            @endif
        </div>

        {{-- Vue Tablet (Cartes compactes) --}}
        <div class="hidden md:block lg:hidden space-y-3">
            @foreach($productModels as $model)
                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md hover:border-gray-300 transition-all" wire:key="model-tablet-{{ $model->id }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        @php
                            $iconMap = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                            $icon = $iconMap[$model->category->value] ?? 'box';
                        @endphp
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $icon }}" class="w-6 h-6 text-gray-600"></i>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="min-w-0 flex-1">
                                    <h4 class="text-base font-semibold text-gray-900 truncate">{{ $model->name }}</h4>
                                    <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $model->brand }}</p>
                                </div>
                                @if($model->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white text-gray-900 border border-gray-300 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-white text-gray-500 border border-gray-300 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        Inactif
                                    </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <div class="text-center p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Catégorie</p>
                                    @php
                                        $categoryIcons = ['telephone' => '📱', 'tablette' => '💻', 'pc' => '🖥️', 'accessoire' => '🎧'];
                                    @endphp
                                    <p class="text-xl">{{ $categoryIcons[$model->category->value] ?? '📦' }}</p>
                                </div>
                                <div class="text-center p-2.5 rounded-lg border {{ $model->products_in_stock_count < $model->stock_minimum ? 'bg-red-50 border-red-100' : 'bg-gray-50 border-gray-100' }}">
                                    <p class="text-xs {{ $model->products_in_stock_count < $model->stock_minimum ? 'text-red-600' : 'text-gray-500' }} mb-1">Stock</p>
                                    <p class="text-base font-bold {{ $model->products_in_stock_count < $model->stock_minimum ? 'text-red-700' : 'text-gray-900' }} flex items-center justify-center gap-1">
                                        {{ $model->products_in_stock_count ?? 0 }}
                                        @if($model->products_in_stock_count < $model->stock_minimum)
                                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                                        @endif
                                    </p>
                                </div>
                                <div class="text-center p-2.5 bg-gray-50 rounded-lg border border-gray-100">
                                    <p class="text-xs text-gray-500 mb-1">Prix</p>
                                    <p class="text-base font-bold text-gray-900">{{ number_format($model->prix_vente_default / 1000, 0) }}k</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('product-models.show', $model) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    Voir
                                </a>
                                @can('update', $model)
                                    <a href="{{ route('product-models.edit', $model) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                        Modifier
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($productModels->hasPages())
                <div class="px-4 py-3 bg-white border border-gray-200 rounded-lg">
                    {{ $productModels->links() }}
                </div>
            @endif
        </div>

        {{-- Vue Mobile (Cartes optimisées) --}}
        <div class="md:hidden space-y-2.5">
            @foreach($productModels as $model)
                <div class="bg-white border border-gray-200 rounded-lg p-3 active:bg-gray-50 transition-colors" wire:key="model-mobile-{{ $model->id }}">
                    {{-- Header --}}
                    <div class="flex items-start gap-2.5 mb-2.5">
                        @php
                            $iconMap = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                            $icon = $iconMap[$model->category->value] ?? 'box';
                        @endphp
                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $model->name }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $model->brand }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if($model->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-white text-gray-900 border border-gray-300">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-white text-gray-500 border border-gray-300">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Info Grid --}}
                    <div class="grid grid-cols-3 gap-2 mb-2.5">
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <p class="text-[10px] text-gray-500 mb-0.5">Catégorie</p>
                            @php
                                $categoryIcons = ['telephone' => '📱', 'tablette' => '💻', 'pc' => '🖥️', 'accessoire' => '🎧'];
                            @endphp
                            <p class="text-base leading-none">{{ $categoryIcons[$model->category->value] ?? '📦' }}</p>
                        </div>
                        <div class="text-center p-2 rounded-lg {{ $model->products_in_stock_count < $model->stock_minimum ? 'bg-red-50' : 'bg-gray-50' }}">
                            <p class="text-[10px] {{ $model->products_in_stock_count < $model->stock_minimum ? 'text-red-600' : 'text-gray-500' }} mb-0.5">Stock</p>
                            <p class="text-sm font-bold {{ $model->products_in_stock_count < $model->stock_minimum ? 'text-red-700' : 'text-gray-900' }} flex items-center justify-center gap-0.5">
                                {{ $model->products_in_stock_count ?? 0 }}
                                @if($model->products_in_stock_count < $model->stock_minimum)
                                    <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                @endif
                            </p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                            <p class="text-[10px] text-gray-500 mb-0.5">Prix</p>
                            <p class="text-sm font-bold text-gray-900">{{ number_format($model->prix_vente_default / 1000, 0) }}k</p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-2.5 border-t border-gray-100">
                        <a href="{{ route('product-models.show', $model) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 active:text-gray-900 border border-gray-200 active:border-gray-300 rounded-lg transition-colors">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            Voir
                        </a>
                        @can('update', $model)
                            <a href="{{ route('product-models.edit', $model) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 active:text-gray-900 border border-gray-200 active:border-gray-300 rounded-lg transition-colors">
                                <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                Modifier
                            </a>
                        @endcan
                    </div>
                </div>
            @endforeach

            @if($productModels->hasPages())
                <div class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg">
                    {{ $productModels->links() }}
                </div>
            @endif
        </div>
    @endif
</div>