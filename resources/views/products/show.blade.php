<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex-1 min-w-0">
                <h1 class="text-lg sm:text-xl lg:text-2xl font-semibold text-gray-900 truncate">{{ $product->productModel->name }}</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                    {{ $product->productModel->brand }} • ID: #{{ $product->id }}
                </p>
            </div>
            
<div class="flex gap-2 sm:gap-3 flex-shrink-0 flex-wrap">
    @can('update', $product)
        <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs sm:text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="pencil" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
            <span class="hidden sm:inline">Modifier</span>
        </a>
    @endcan
    
    @can('sell', $product)
        @if($product->isAvailable())
            {{-- Vente Rapide --}}
            <a 
                href="{{ route('products.quick-sell', $product) }}" 
                class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-green-600 border border-green-600 rounded-lg font-medium text-xs sm:text-sm text-white hover:bg-green-700 active:bg-green-800 transition-colors shadow-sm hover:shadow"
                title="Vente rapide : Client normal avec paiement immédiat"
            >
                <i data-lucide="zap" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                <span>Vente Rapide</span>
            </a>
            
            {{-- Vente Complète --}}
            <a 
                href="{{ route('sales.create', ['product' => $product->id]) }}" 
                class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 border border-blue-600 rounded-lg font-medium text-xs sm:text-sm text-white hover:bg-blue-700 active:bg-blue-800 transition-colors shadow-sm hover:shadow"
                title="Vente complète : Revendeur, crédit ou dépôt"
            >
                <i data-lucide="file-text" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                <span class="hidden xs:inline">Vente Complète</span>
                <span class="xs:hidden">Complète</span>
            </a>
        @endif
    @endcan
</div>
        </div>
    </x-slot>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
            {{-- État et localisation --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="activity" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Statut actuel</h3>
                </div>
                <div class="flex flex-wrap gap-2 sm:gap-3">
                    <x-products.state-badge :state="$product->state" class="text-xs sm:text-sm px-3 sm:px-4 py-1.5 sm:py-2" />
                    <x-products.location-badge :location="$product->location" class="text-xs sm:text-sm px-3 sm:px-4 py-1.5 sm:py-2" />
                </div>
            </div>

            {{-- Détails produit --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="info" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Détails du produit</h3>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                            <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                            IMEI
                        </dt>
                        @if($product->imei)
                            @php
                                // Formater l'IMEI : 12 345678 910111 2
                                $imei = preg_replace('/\s+/', '', $product->imei); // Retirer les espaces existants
                                $formattedImei = '';
                                if (strlen($imei) >= 15) {
                                    $formattedImei = substr($imei, 0, 2) . ' ' . substr($imei, 2, 6) . ' ' . substr($imei, 8, 6) . ' ' . substr($imei, 14);
                                } else {
                                    $formattedImei = $imei;
                                }
                            @endphp
                            <dd class="relative group">
                                <div class="text-md sm:text-base lg:text-2xl text-gray-900 font-mono font-semibold bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-3 sm:py-4 rounded-lg border-2 border-gray-300 tracking-wide break-all selection:bg-blue-200 hover:border-gray-400 transition-colors">
                                    {{ $formattedImei }}
                                </div>
                                <button 
                                    onclick="navigator.clipboard.writeText('{{ $product->imei }}'); this.querySelector('.copy-text').textContent = 'Copié !'; setTimeout(() => this.querySelector('.copy-text').textContent = 'Copier', 2000);"
                                    class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded-md text-xs font-medium text-gray-700 shadow-sm hover:shadow transition-all opacity-0 group-hover:opacity-100"
                                    title="Copier l'IMEI"
                                >
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    <span class="copy-text">Copier</span>
                                </button>
                            </dd>
                        @else
                            <dd class="text-sm text-gray-400 italic bg-gray-50 px-4 py-3 rounded-lg border border-dashed border-gray-300">
                                Non renseigné
                            </dd>
                        @endif
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-2">
                            <i data-lucide="barcode" class="w-3.5 h-3.5"></i>
                            Numéro de série
                        </dt>
                        @if($product->serial_number)
                            <dd class="relative group">
                                <div class="text-sm sm:text-base lg:text-lg text-gray-900 font-mono font-semibold bg-gradient-to-br from-gray-50 to-gray-100 px-4 py-3 sm:py-4 rounded-lg border-2 border-gray-300 tracking-wider break-all selection:bg-blue-200 hover:border-gray-400 transition-colors">
                                    {{ $product->serial_number }}
                                </div>
                                <button 
                                    onclick="navigator.clipboard.writeText('{{ $product->serial_number }}'); this.querySelector('.copy-text').textContent = 'Copié !'; setTimeout(() => this.querySelector('.copy-text').textContent = 'Copier', 2000);"
                                    class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-white hover:bg-gray-50 border border-gray-300 rounded-md text-xs font-medium text-gray-700 shadow-sm hover:shadow transition-all opacity-0 group-hover:opacity-100"
                                    title="Copier le numéro de série"
                                >
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    <span class="copy-text">Copier</span>
                                </button>
                            </dd>
                        @else
                            <dd class="text-sm text-gray-400 italic bg-gray-50 px-4 py-3 rounded-lg border border-dashed border-gray-300">
                                Non renseigné
                            </dd>
                        @endif
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Condition</dt>
                        <dd class="text-xs sm:text-sm text-gray-900 font-medium">{{ $product->condition ?: 'N/A' }}</dd>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <div>
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Fournisseur</dt>
                            <dd class="text-xs sm:text-sm text-gray-900">{{ $product->fournisseur ?: 'N/A' }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Date d'achat</dt>
                        <dd class="text-xs sm:text-sm text-gray-900">{{ $product->date_achat?->format('d/m/Y') ?: 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">En stock depuis</dt>
                        <dd class="text-xs sm:text-sm text-gray-900 font-medium">{{ $stats['days_in_stock_human'] ?? 'N/A' }}</dd>
                    </div>
                </dl>

                @if($product->defauts)
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Défauts constatés</dt>
                        <dd class="text-xs sm:text-sm text-gray-900 bg-red-50 border border-red-100 rounded p-3">{{ $product->defauts }}</dd>
                    </div>
                @endif

                @if($product->notes)
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-6 border-t border-gray-200">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</dt>
                        <dd class="text-xs sm:text-sm text-gray-900 bg-gray-50 border border-gray-200 rounded p-3">{{ $product->notes }}</dd>
                    </div>
                @endif
            </div>

            {{-- Historique des mouvements --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="history" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Historique des mouvements</h3>
                </div>

                <div class="flow-root">
                    <ul role="list" class="space-y-4 sm:space-y-6">
                        @forelse($product->stockMovements as $movement)
                            <li>
                                <div class="relative flex gap-3 sm:gap-4">
                                    @if(!$loop->last)
                                        <div class="absolute left-4 sm:left-5 top-10 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                                    @endif
                                    <div class="relative flex h-8 w-8 sm:h-10 sm:w-10 flex-none items-center justify-center bg-gray-900 rounded-full">
                                        <i data-lucide="arrow-right-left" class="h-4 w-4 sm:h-5 sm:w-5 text-white"></i>
                                    </div>
                                    <div class="flex-auto rounded-lg border border-gray-200 p-3 sm:p-4">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 sm:gap-4">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs sm:text-sm font-semibold text-gray-900">{{ $movement->type->label() }}</p>
                                                @if($movement->notes)
                                                    <p class="text-xs sm:text-sm text-gray-600 mt-1 break-words">{{ $movement->notes }}</p>
                                                @endif
                                                <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                                                    <i data-lucide="user" class="w-3 h-3 flex-shrink-0"></i>
                                                    <span class="truncate">{{ $movement->user->name }}</span>
                                                </p>
                                            </div>
                                            <div class="text-left sm:text-right flex-shrink-0">
                                                <time class="text-xs text-gray-500 whitespace-nowrap block">
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
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="inbox" class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400"></i>
                                </div>
                                <p class="text-xs sm:text-sm text-gray-500">Aucun mouvement enregistré</p>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4 sm:space-y-6">
            {{-- Prix et marges --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="coins" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    @if(auth()->user()->hasRole('admin'))
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Prix & Marges</h3>
                    @else
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Prix</h3>
                    @endif
                </div>

                <dl class="space-y-3 sm:space-y-4">
                    @if(auth()->user()->hasRole('admin'))
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prix d'achat</dt>
                            <dd class="text-lg sm:text-xl font-bold text-gray-900">{{ number_format($product->prix_achat, 0, ',', ' ') }}</dd>
                            <span class="text-xs text-gray-500">FCFA</span>
                        </div>
                    @endif

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Prix de vente</dt>
                        <dd class="text-xl sm:text-2xl font-bold text-gray-900">{{ number_format($product->prix_vente, 0, ',', ' ') }}</dd>
                        <span class="text-xs text-gray-500">FCFA</span>
                    </div>

                    @if(auth()->user()->hasRole('admin'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                            <dt class="text-xs font-medium text-green-700 uppercase tracking-wide mb-1 flex items-center gap-1">
                                <i data-lucide="trending-up" class="w-3 h-3"></i>
                                Bénéfice potentiel
                            </dt>
                            <dd class="text-xl sm:text-2xl font-bold text-green-600">+{{ number_format($product->benefice_potentiel, 0, ',', ' ') }}</dd>
                            <span class="text-xs text-green-600">FCFA</span>
                        </div>

                        <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Marge</dt>
                            <dd class="text-base sm:text-lg font-bold text-gray-900">{{ $product->marge_percentage }}%</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Statistiques --}}
            <div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6">
                <div class="flex items-center gap-3 mb-4 sm:mb-6">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gray-900 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                    </div>
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-900 uppercase tracking-wide">Statistiques</h3>
                </div>

                <dl class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs sm:text-sm text-gray-600">Mouvements de stock</dt>
                        <dd class="text-xs sm:text-sm font-semibold text-gray-900">{{ $stats['total_movements'] }}</dd>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <dt class="text-xs sm:text-sm text-gray-600">Disponible</dt>
                        <dd>
                            @if($stats['is_available'])
                                <span class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    <span class="hidden sm:inline">Oui</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 sm:px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                    <span class="hidden sm:inline">Non</span>
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Zone de danger --}}
            @can('delete', $product)
                <div class="bg-white border border-red-200 rounded-lg p-4 sm:p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-red-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="alert-triangle" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                        </div>
                        <h3 class="text-xs sm:text-sm font-semibold text-red-900 uppercase tracking-wide">Zone de danger</h3>
                    </div>

                    <p class="text-xs text-gray-600 mb-4">
                        La suppression de ce produit est irréversible. Toutes les données associées seront perdues.
                    </p>

                    <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('⚠️ Êtes-vous absolument sûr de vouloir supprimer ce produit ?\n\nCette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 border border-red-600 rounded-md font-medium text-xs sm:text-sm text-white hover:bg-red-700 transition-colors">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                            Supprimer
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
</x-app-layout>