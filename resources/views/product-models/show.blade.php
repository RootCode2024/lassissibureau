<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-gradient-to-br from-gray-900 to-gray-700 rounded-xl flex items-center justify-center shadow-sm">
                <i data-lucide="box" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $productModel->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $productModel->brand }}</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        @can('update', $productModel)
            <a href="{{ route('product-models.edit', $productModel) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg font-medium text-sm hover:bg-gray-800 transition-all hover:shadow-lg hover:scale-105">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                Modifier
            </a>
        @endcan
    </x-slot>

    <x-alerts.success :message="session('success')" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Informations générales --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Informations générales</h3>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-gray-500">Nom du modèle</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productModel->name }}</dd>
                        </div>

                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-gray-500">Marque</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productModel->brand }}</dd>
                        </div>

                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-gray-500">Catégorie</dt>
                            <dd>
                                @php
                                    $categoryLabels = [
                                        'telephone' => ['icon' => '📱', 'label' => 'Téléphone'],
                                        'tablette' => ['icon' => '💻', 'label' => 'Tablette'],
                                        'pc' => ['icon' => '🖥️', 'label' => 'Ordinateur'],
                                        'accessoire' => ['icon' => '🎧', 'label' => 'Accessoire'],
                                    ];
                                    $category = $categoryLabels[$productModel->category] ?? ['icon' => '📦', 'label' => ucfirst($productModel->category)];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200 hover:border-gray-300 transition-colors">
                                    <span>{{ $category['icon'] }}</span>
                                    {{ $category['label'] }}
                                </span>
                            </dd>
                        </div>

                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-gray-500">Statut</dt>
                            <dd>
                                @if($productModel->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                        <span class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                        </span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                        Inactif
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div class="space-y-1">
                            <dt class="text-xs font-medium text-gray-500">Stock minimum</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $productModel->stock_minimum }} unités</dd>
                        </div>
                    </dl>

                    @if($productModel->description)
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <dt class="text-xs font-medium text-gray-500 mb-2">Description</dt>
                            <dd class="text-sm text-gray-600 leading-relaxed">{{ $productModel->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Prix avec design moderne --}}
            <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6">
                    <h3 class="text-base font-semibold text-white mb-6">Tarification par défaut</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                            <dt class="text-xs font-medium text-gray-300 mb-2">Prix d'achat</dt>
                            <dd class="text-2xl font-bold text-white">{{ number_format($productModel->prix_revient_default, 0, ',', ' ') }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">FCFA</dd>
                        </div>

                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/20">
                            <dt class="text-xs font-medium text-gray-300 mb-2">Prix de vente</dt>
                            <dd class="text-2xl font-bold text-white">{{ number_format($productModel->prix_vente_default, 0, ',', ' ') }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">FCFA</dd>
                        </div>

                        <div class="bg-green-500/20 backdrop-blur-sm rounded-lg p-4 border border-green-400/30">
                            @php
                                $benefice = $productModel->prix_vente_default - $productModel->prix_revient_default;
                                $marge = $productModel->prix_revient_default > 0 ? ($benefice / $productModel->prix_revient_default) * 100 : 0;
                            @endphp
                            <dt class="text-xs font-medium text-green-300 mb-2">Bénéfice</dt>
                            <dd class="text-2xl font-bold text-green-100">{{ number_format($benefice, 0, ',', ' ') }}</dd>
                            <dd class="text-xs text-green-300 mt-1">+{{ number_format($marge, 1) }}% ROI</dd>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Liste des produits avec design amélioré et regroupement --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">Produits en stock</h3>
                            <p class="text-sm text-gray-500 mt-0.5">
                                {{ $productModel->products->count() }} {{ $productModel->products->count() > 1 ? 'produits' : 'produit' }} au total
                            </p>
                        </div>
                        @if($productModel->products->count() > 0)
                            <a href="{{ route('products.index', ['product_model_id' => $productModel->id]) }}"
                               class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors">
                                Voir tout →
                            </a>
                        @endif
                    </div>
                </div>

                @if($productModel->products->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16 px-6">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i data-lucide="package" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-900 mb-1">Aucun produit</p>
                        <p class="text-sm text-gray-500 text-center">
                            Commencez par ajouter des produits à ce modèle
                        </p>
                    </div>
                @else
                    @php
                        // Grouper les produits par date/heure de création (à la minute près)
                        $groupedProducts = $productModel->products
                            ->sortByDesc('created_at')
                            ->take(50) // Limiter pour les performances
                            ->groupBy(function($product) {
                                return $product->created_at->format('Y-m-d H:i');
                            });

                        $displayedGroups = 0;
                        $maxGroups = 10;
                    @endphp

                    <div class="divide-y divide-gray-100">
                        @foreach($groupedProducts as $dateTime => $products)
                            @if($displayedGroups < $maxGroups)
                                @php
                                    $firstProduct = $products->first();
                                    $productCount = $products->count();
                                    $displayedGroups++;
                                @endphp

                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    {{-- En-tête du groupe avec date/heure --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg flex items-center justify-center border border-blue-100">
                                                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="text-xs font-medium text-gray-900">
                                                    {{ $firstProduct->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $productCount }} {{ $productCount > 1 ? 'produits ajoutés' : 'produit ajouté' }}
                                                </p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $productCount }}
                                        </span>
                                    </div>

                                    {{-- Liste des produits du groupe --}}
                                    <div class="ml-10 space-y-2">
                                        @foreach($products as $product)
                                            <a
                                                href="{{ route('products.show', $product) }}"
                                                class="group flex items-center gap-3 p-3 rounded-lg hover:bg-white border border-transparent hover:border-gray-200 transition-all"
                                            >
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-gray-900 transition-colors">
                                                        <i data-lucide="smartphone" class="w-4 h-4 text-gray-500 group-hover:text-white transition-colors"></i>
                                                    </div>
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 font-mono mb-1">
                                                        {{ $product->imei ?: $product->serial_number ?: 'N/A' }}
                                                    </p>

                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <x-products.state-badge :state="$product->state" class="text-xs" />
                                                        <x-products.location-badge :location="$product->location" class="text-xs" />
                                                    </div>
                                                </div>

                                                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-gray-600 transition-colors"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    @if($productModel->products->count() > 50 || $groupedProducts->count() > $maxGroups)
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            <a
                                href="{{ route('products.index', ['product_model_id' => $productModel->id]) }}"
                                class="flex items-center justify-center gap-2 text-sm font-medium text-gray-700 hover:text-gray-900 py-2 transition-colors"
                            >
                                Voir tous les produits
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @endif
                @endif
            </div>

        </div>

        {{-- Sidebar avec design moderne --}}
        <div class="space-y-6">

            {{-- Statistiques avec cards --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-900">Statistiques</h3>
                </div>

                <div class="p-6 space-y-6">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-100">
                        <div class="flex items-center justify-between mb-2">
                            <dt class="text-xs font-medium text-blue-700">Stock actuel</dt>
                            <i data-lucide="package" class="w-4 h-4 text-blue-400"></i>
                        </div>
                        <dd class="text-3xl font-bold text-blue-900">{{ $stats['total_stock'] }}</dd>
                        <p class="text-xs text-blue-600 mt-1">unités disponibles</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 border border-green-100">
                        <div class="flex items-center justify-between mb-2">
                            <dt class="text-xs font-medium text-green-700">Total vendu</dt>
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-400"></i>
                        </div>
                        <dd class="text-3xl font-bold text-green-900">{{ $stats['total_sold'] }}</dd>
                        <p class="text-xs text-green-600 mt-1">ventes réalisées</p>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-100">
                        <div class="flex items-center justify-between mb-2">
                            <dt class="text-xs font-medium text-purple-700">Prix moyen</dt>
                            <i data-lucide="coins" class="w-4 h-4 text-purple-400"></i>
                        </div>
                        <dd class="text-2xl font-bold text-purple-900">
                            {{ number_format($stats['average_price'] ?? 0, 0, ',', ' ') }}
                        </dd>
                        <p class="text-xs text-purple-600 mt-1">FCFA par unité</p>
                    </div>
                </div>
            </div>

            {{-- Alerte stock avec design moderne --}}
            @if($stats['total_stock'] < $productModel->stock_minimum)
                <div class="bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-6 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-red-900 mb-1">⚠️ Stock critique</h4>
                            <p class="text-sm text-red-700 leading-relaxed">
                                Seuil minimum atteint : <strong>{{ $stats['total_stock'] }}/{{ $productModel->stock_minimum }}</strong> unités
                            </p>
                            <button class="mt-3 text-xs font-medium text-red-700 hover:text-red-900 underline underline-offset-2">
                                Réapprovisionner →
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-green-900 mb-1">✓ Stock optimal</h4>
                            <p class="text-sm text-green-700">
                                Le niveau de stock est satisfaisant
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions dangereuses avec nouveau style --}}
            @can('delete', $productModel)
                <div class="bg-white border-2 border-red-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="alert-octagon" class="w-4 h-4 text-red-600"></i>
                            </div>
                            <h3 class="text-sm font-semibold text-gray-900">Zone dangereuse</h3>
                        </div>
                        <p class="text-xs text-gray-600 mb-4 leading-relaxed">
                            La suppression est définitive et irréversible
                        </p>

                        <form method="POST" action="{{ route('product-models.destroy', $productModel) }}"
                              onsubmit="return confirm('⚠️ Confirmer la suppression de ce modèle ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border-2 border-red-600 rounded-lg font-medium text-sm text-red-600 hover:bg-red-600 hover:text-white transition-all hover:shadow-md">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Supprimer définitivement
                            </button>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
