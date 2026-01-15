<div>
    {{-- Statistiques --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Total Modèles</p>
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
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Actifs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['actifs'] }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Stock Faible</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['stock_faible'] }}</p>
                </div>
                <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Catégories</p>
                    <p class="text-3xl font-bold text-gray-900">4</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="grid" class="w-5 h-5 text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-dashed border-gray-300 rounded-lg mb-6 p-6">
        <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wide mb-4">Filtres de recherche</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Recherche --}}
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Recherche
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Rechercher par nom, marque..."
                        class="block w-full pl-10 pr-4 py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                    >
                    @if($search)
                        <button
                            wire:click="$set('search', '')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i data-lucide="x" class="w-4 h-4 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Catégorie --}}
            <div>
                <label class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Catégorie
                </label>
                <select
                    wire:model.live="category"
                    class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
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
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-md font-medium text-sm text-gray-700 transition-colors"
                >
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Produit
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Catégorie
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Stock
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Prix de vente
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($productModels as $model)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Produit --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        $iconMap = [
                                            'telephone' => 'smartphone',
                                            'tablette' => 'tablet',
                                            'pc' => 'monitor',
                                            'accessoire' => 'box'
                                        ];
                                        $icon = $iconMap[$model->category] ?? 'box';
                                    @endphp
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $model->name }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $model->brand }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Catégorie --}}
                            <td class="px-6 py-4">
                                @php
                                    $categoryLabels = [
                                        'telephone' => '📱 Téléphone',
                                        'tablette' => '💻 Tablette',
                                        'pc' => '🖥️ Ordinateur',
                                        'accessoire' => '🎧 Accessoire',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $categoryLabels[$model->category] ?? ucfirst($model->category) }}
                                </span>
                            </td>

                            {{-- Stock --}}
                            <td class="px-6 py-4 text-center">
                                @if($model->products_in_stock_count < $model->stock_minimum)
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-red-50 border border-red-200 rounded-md">
                                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-red-600"></i>
                                        <span class="text-sm font-semibold text-red-700">{{ $model->products_in_stock_count ?? 0 }}</span>
                                    </div>
                                @else
                                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-200 rounded-md">
                                        <span class="text-sm font-semibold text-gray-900">{{ $model->products_in_stock_count ?? 0 }}</span>
                                    </div>
                                @endif
                            </td>

                            {{-- Prix --}}
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($model->prix_vente_default, 0, ',', ' ') }}
                                </div>
                                <div class="text-xs text-gray-500">FCFA</div>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4 text-center">
                                @if($model->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium bg-white text-gray-900 border border-gray-300">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded text-xs font-medium bg-white text-gray-500 border border-gray-300">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                        Inactif
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a
                                        href="{{ route('product-models.show', $model) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 rounded-md transition-colors"
                                    >
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        Voir
                                    </a>
                                    @can('update', $model)
                                        <a
                                            href="{{ route('product-models.edit', $model) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 rounded-md transition-colors"
                                        >
                                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                            Modifier
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm font-medium text-gray-900">Aucun modèle trouvé</p>
                                        <p class="text-xs text-gray-500 mt-1">Essayez de modifier vos critères de recherche</p>
                                    </div>
                                    @if($search || $category)
                                        <button
                                            wire:click="resetFilters"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 text-white text-sm font-medium rounded-md transition-colors"
                                        >
                                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                            Réinitialiser les filtres
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($productModels->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $productModels->links() }}
            </div>
        @endif
    </div>
</div>
