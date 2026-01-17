<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-white/80 backdrop-blur-xl sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Retours Fournisseurs</h1>
                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">Gérez les retours et suivez les produits défectueux</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="px-3 sm:px-4 py-2 bg-gray-50 rounded-lg border border-gray-200">
                            <span class="text-xs font-medium text-gray-500">Total à traiter</span>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $toReturn->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8 space-y-6 sm:space-y-8">
            
            <!-- Section 1: À Renvoyer -->
            <div class="group">
                <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4 flex-wrap">
                    <div class="w-1 h-5 sm:h-6 bg-gradient-to-b from-red-500 to-red-600 rounded-full"></div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Produits défectueux</h2>
                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-red-50 text-red-700 text-xs font-semibold rounded-full border border-red-200">
                        {{ $toReturn->count() }} en attente
                    </span>
                </div>
                
                @if($toReturn->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-xl sm:rounded-2xl p-8 sm:p-12 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-gray-500 font-medium">Aucun produit en attente de retour</p>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">Tous vos produits sont en bon état</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="lg:hidden space-y-3">
                        @foreach($toReturn as $product)
                            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $product->productModel->name }}</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $product->fournisseur ?? 'Fournisseur inconnu' }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">IMEI</p>
                                        <code class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded-lg border border-gray-200 block truncate">
                                            {{ $product->imei }}
                                        </code>
                                    </div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">État</p>
                                        <x-products.state-badge :state="$product->state" />
                                    </div>
                                    
                                    @if($product->notes || $product->defauts)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Détails</p>
                                        <p class="text-sm text-gray-600 line-clamp-2">
                                            {{ $product->notes ?? $product->defauts }}
                                        </p>
                                    </div>
                                    @endif
                                </div>
                                
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                    Traiter
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden lg:block bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IMEI</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">État</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Détails</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($toReturn as $product)
                                        <tr class="group/row hover:bg-gray-50/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $product->productModel->name }}</div>
                                                        <div class="text-xs text-gray-500 mt-0.5">{{ $product->fournisseur ?? 'Fournisseur inconnu' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <code class="px-2.5 py-1.5 bg-gray-100 text-gray-800 text-xs font-mono rounded-lg border border-gray-200">
                                                    {{ $product->imei }}
                                                </code>
                                            </td>
                                            <td class="px-6 py-4">
                                                <x-products.state-badge :state="$product->state" />
                                            </td>
                                            <td class="px-6 py-4">
                                                <p class="text-sm text-gray-600 max-w-xs truncate">
                                                    {{ $product->notes ?? $product->defauts ?? 'Aucun détail' }}
                                                </p>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('products.edit', $product) }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md group-hover/row:translate-x-0.5">
                                                    Traiter
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Section 2: Déjà chez le fournisseur -->
            <div class="group">
                <div class="flex items-center gap-2 sm:gap-3 mb-3 sm:mb-4 flex-wrap">
                    <div class="w-1 h-5 sm:h-6 bg-gradient-to-b from-blue-500 to-blue-600 rounded-full"></div>
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Chez le fournisseur</h2>
                    <span class="px-2 sm:px-2.5 py-0.5 sm:py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-full border border-blue-200">
                        {{ $atSupplier->count() }} en cours
                    </span>
                </div>

                @if($atSupplier->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-xl sm:rounded-2xl p-8 sm:p-12 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-gray-500 font-medium">Aucun produit chez le fournisseur</p>
                        <p class="text-xs sm:text-sm text-gray-400 mt-1">L'historique apparaîtra ici</p>
                    </div>
                @else
                    <!-- Mobile Cards -->
                    <div class="lg:hidden space-y-3">
                        @foreach($atSupplier as $product)
                            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $product->productModel->name }}</h3>
                                        <div class="flex items-center gap-1.5 text-xs text-gray-500 mt-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $product->updated_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 mb-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">IMEI</p>
                                        <code class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-mono rounded-lg border border-gray-200 block truncate">
                                            {{ $product->imei }}
                                        </code>
                                    </div>
                                    
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Fournisseur</p>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-lg border border-gray-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                            {{ $product->fournisseur ?? 'Non spécifié' }}
                                        </span>
                                    </div>
                                </div>
                                
                                <a href="{{ route('products.show', $product) }}" 
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-200 transition-all duration-200">
                                    Voir détails
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- Desktop Table -->
                    <div class="hidden lg:block bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50/50">
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date envoi</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IMEI</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Fournisseur</th>
                                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($atSupplier as $product)
                                        <tr class="group/row hover:bg-gray-50/50 transition-colors duration-150">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ $product->updated_at->format('d/m/Y') }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-1 ml-6">
                                                    {{ $product->updated_at->diffForHumans() }}
                                                </p>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                        </svg>
                                                    </div>
                                                    <span class="font-semibold text-gray-900">{{ $product->productModel->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <code class="px-2.5 py-1.5 bg-gray-100 text-gray-800 text-xs font-mono rounded-lg border border-gray-200">
                                                    {{ $product->imei }}
                                                </code>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg border border-gray-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                                    </svg>
                                                    {{ $product->fournisseur ?? 'Non spécifié' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('products.show', $product) }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-200 transition-all duration-200 hover:shadow-sm group-hover/row:translate-x-0.5">
                                                    Voir détails
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>