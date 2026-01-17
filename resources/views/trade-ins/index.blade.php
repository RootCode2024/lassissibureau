<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-white/80 backdrop-blur-xl sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Historique des Trocs</h1>
                        <p class="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-600">Consultez tous les échanges et reprises effectués</p>
                    </div>
                    <a href="{{ route('trade-ins.pending') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-purple-500/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Trocs en attente</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
            @if($tradeIns->isEmpty())
                <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium">Aucun troc trouvé</p>
                    <p class="text-sm text-gray-400 mt-1">L'historique des trocs est vide pour le moment</p>
                </div>
            @else
                <!-- Mobile Cards -->
                <div class="lg:hidden space-y-4">
                    @foreach($tradeIns as $tradeIn)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-300">
                            <!-- Header with date and status -->
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">{{ $tradeIn->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                @if($tradeIn->isPending())
                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        En attente
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        Traité
                                    </span>
                                @endif
                            </div>

                            <!-- Product info -->
                            <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-100">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $tradeIn->modele_recu }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <code class="px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-mono rounded border border-gray-200">
                                            {{ $tradeIn->imei_recu ?? 'N/A' }}
                                        </code>
                                        @if($tradeIn->etat_recu)
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-medium rounded border border-blue-200">
                                                {{ $tradeIn->etat_recu }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Details grid -->
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Valeur reprise</p>
                                    <p class="text-sm font-bold text-gray-900">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</p>
                                    @if($tradeIn->complement_especes > 0)
                                        <p class="text-xs text-green-600 mt-0.5">+ {{ number_format($tradeIn->complement_especes, 0, ',', ' ') }} FCFA</p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Client</p>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $tradeIn->sale->client_name ?? 'Client inconnu' }}</p>
                                    @if($tradeIn->sale && $tradeIn->sale->product && $tradeIn->sale->product->productModel)
                                        <p class="text-xs text-gray-500 truncate mt-0.5">
                                            {{ $tradeIn->sale->product->productModel->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            @if($tradeIn->isPending())
                                <a href="{{ route('trade-ins.create-product', $tradeIn) }}" 
                                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                    Traiter le troc
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                @if($tradeIn->productReceived)
                                    <a href="{{ route('products.show', $tradeIn->productReceived) }}" 
                                       class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-200 transition-all duration-200">
                                        Voir le produit
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Desktop Table -->
                <div class="hidden lg:block bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Produit Repris</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Valeur Reprise</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Client / Vente</th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($tradeIns as $tradeIn)
                                    <tr class="group/row hover:bg-gray-50/50 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ $tradeIn->created_at->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500 ml-6 mt-0.5">
                                                {{ $tradeIn->created_at->format('H:i') }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-900">{{ $tradeIn->modele_recu }}</div>
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <code class="px-2 py-0.5 bg-gray-100 text-gray-800 text-xs font-mono rounded border border-gray-200">
                                                            {{ $tradeIn->imei_recu ?? 'N/A' }}
                                                        </code>
                                                        @if($tradeIn->etat_recu)
                                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-medium rounded border border-blue-200">
                                                                {{ $tradeIn->etat_recu }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-900">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</div>
                                            @if($tradeIn->complement_especes > 0)
                                                <div class="text-xs text-green-600 mt-1">+ {{ number_format($tradeIn->complement_especes, 0, ',', ' ') }} FCFA (Client)</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $tradeIn->sale->client_name ?? 'Client inconnu' }}</div>
                                            @if($tradeIn->sale && $tradeIn->sale->product && $tradeIn->sale->product->productModel)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Achat: {{ $tradeIn->sale->product->productModel->name }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($tradeIn->isPending())
                                                <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                    En attente
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex items-center text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                                    Traité
                                                </span>
                                                @if($tradeIn->productReceived)
                                                    <a href="{{ route('products.show', $tradeIn->productReceived) }}" class="block text-xs text-blue-600 hover:text-blue-900 mt-1.5 font-medium">
                                                        Voir produit →
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($tradeIn->isPending())
                                                <a href="{{ route('trade-ins.create-product', $tradeIn) }}" 
                                                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md group-hover/row:translate-x-0.5">
                                                    Traiter
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $tradeIns->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>