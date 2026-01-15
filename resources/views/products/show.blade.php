<x-app-layout>
    <x-slot name="header">
        {{ $product->productModel->name }}
    </x-slot>

    <x-slot name="actions">
        <div class="flex gap-3">
            @can('update', $product)
                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="pencil" class="w-4 h-4"></i>
                    Modifier
                </a>
            @endcan
            @can('sell', $product)
                @if($product->isAvailable())
                    <a href="{{ route('products.quick-sell', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black border border-black rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors">
                        <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                        Vendre
                    </a>
                @endif
            @endcan
        </div>
    </x-slot>

    <!-- Sous-titre -->
    <div class="mb-6">
        <p class="text-sm text-gray-500">
            {{ $product->productModel->brand }} • ID: #{{ $product->id }}
        </p>
    </div>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- État et localisation --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="activity" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Statut actuel</h3>
                </div>
                <div class="flex flex-wrap gap-3">
                    <x-products.state-badge :state="$product->state" class="text-sm px-4 py-2" />
                    <x-products.location-badge :location="$product->location" class="text-sm px-4 py-2" />
                </div>
            </div>

            {{-- Détails produit --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="info" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Détails du produit</h3>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">IMEI</dt>
                        <dd class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded border border-gray-200">
                            {{ $product->imei ?: 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Numéro de série</dt>
                        <dd class="text-sm text-gray-900 font-mono bg-gray-50 px-3 py-2 rounded border border-gray-200">
                            {{ $product->serial_number ?: 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Condition</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $product->condition ?: 'N/A' }}</dd>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Fournisseur</dt>
                            <dd class="text-sm text-gray-900">{{ $product->fournisseur ?: 'N/A' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Date d'achat</dt>
                        <dd class="text-sm text-gray-900">{{ $product->date_achat?->format('d/m/Y') ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">En stock depuis</dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $stats['days_in_stock_human'] ?? 'N/A' }}</dd>
                    </div>
                </dl>

                @if($product->defauts)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Défauts constatés</dt>
                        <dd class="text-sm text-gray-900 bg-red-50 border border-red-100 rounded p-3">{{ $product->defauts }}</dd>
                    </div>
                @endif

                @if($product->notes)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</dt>
                        <dd class="text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded p-3">{{ $product->notes }}</dd>
                    </div>
                @endif
            </div>

            {{-- Historique des mouvements --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="history" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Historique des mouvements</h3>
                </div>

                <div class="flow-root">
                    <ul role="list" class="space-y-6">
                        @forelse($product->stockMovements as $movement)
                            <li>
                                <div class="relative flex gap-4">
                                    @if(!$loop->last)
                                        <div class="absolute left-5 top-10 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                                    @endif
                                    <div class="relative flex h-10 w-10 flex-none items-center justify-center bg-gray-900 rounded-full">
                                        <i data-lucide="arrow-right-left" class="h-5 w-5 text-white"></i>
                                    </div>
                                    <div class="flex-auto rounded-lg border border-gray-200 p-4">
                                        <div class="flex justify-between items-start gap-4">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ $movement->type->label() }}</p>
                                                @if($movement->notes)
                                                    <p class="text-sm text-gray-600 mt-1">{{ $movement->notes }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                                    <i data-lucide="user" class="w-3 h-3"></i>
                                                    {{ $movement->user->name }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <time class="text-xs text-gray-500 whitespace-nowrap">
                                                    {{ $movement->created_at->format('d/m/Y') }}
                                                </time>
                                                <p class="text-xs text-gray-400">
                                                    {{ $movement->created_at->format('H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="text-center py-8">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
                                </div>
                                <p class="text-sm text-gray-500">Aucun mouvement enregistré</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Prix et marges --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="coins" class="w-5 h-5 text-white"></i>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Prix, Marges & Bénéfices</h3>
                    @else
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Prix</h3>
                    @endif
                </div>

                <dl class="space-y-4">
                    @if(auth()->user()->hasRole('admin'))
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prix d'achat</dt>
                            <dd class="text-xl font-bold text-gray-900">{{ number_format($product->prix_achat, 0, ',', ' ') }}</dd>
                            <span class="text-xs text-gray-500">FCFA</span>
                        </div>
                    @endif

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prix de vente</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ number_format($product->prix_vente, 0, ',', ' ') }}</dd>
                        <span class="text-xs text-gray-500">FCFA</span>
                    </div>

                    @if(auth()->user()->hasRole('admin'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <dt class="text-xs font-medium text-green-700 uppercase tracking-wide mb-1 flex items-center gap-1">
                                <i data-lucide="trending-up" class="w-3 h-3"></i>
                                Bénéfice potentiel
                            </dt>
                            <dd class="text-2xl font-bold text-green-600">+{{ number_format($product->benefice_potentiel, 0, ',', ' ') }}</dd>
                            <span class="text-xs text-green-600">FCFA</span>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Marge</dt>
                            <dd class="text-lg font-bold text-gray-900">{{ $product->marge_percentage }}%</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Statistiques --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Statistiques</h3>
                </div>

                <dl class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <dt class="text-sm text-gray-600">Mouvements de stock</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $stats['total_movements'] }}</dd>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <dt class="text-sm text-gray-600">Disponible à la vente</dt>
                        <dd>
                            @if($stats['is_available'])
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Oui
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                    Non
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Zone de danger --}}
            @can('delete', $product)
                <div class="bg-white border border-red-200 rounded-lg p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                        </div>
                        <h3 class="text-sm font-semibold text-red-900 uppercase tracking-wide">Zone de danger</h3>
                    </div>

                    <p class="text-xs text-gray-600 mb-4">
                        La suppression de ce produit est irréversible. Toutes les données associées seront perdues.
                    </p>

                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('⚠️ Êtes-vous absolument sûr de vouloir supprimer ce produit ?\n\nCette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 border border-red-600 rounded-md font-medium text-sm text-white hover:bg-red-700 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Supprimer le produit
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>
