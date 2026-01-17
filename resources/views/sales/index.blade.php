<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Ventes</h2>
                <p class="text-sm text-gray-500 mt-1">Gérez et suivez toutes vos ventes</p>
            </div>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-black text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-all duration-200 shadow-sm hover:shadow-md">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Nouvelle vente</span>
            </a>
        </div>
    </x-slot>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    {{-- Statistiques --}}
    <div class="grid grid-cols-2 lg:grid-cols-{{ auth()->user()->isAdmin() ? '4' : '2' }} gap-4 sm:gap-6 mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ventes du jour</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $stats['today'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">vente{{ ($stats['today'] ?? 0) > 1 ? 's' : '' }} aujourd'hui</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ventes du mois</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-2">{{ $stats['month'] ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">ce mois-ci</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                    <i data-lucide="calendar" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin())
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">CA du mois</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-2 truncate">{{ number_format($stats['revenue'] ?? 0, 0, ',', ' ') }}</p>
                    <p class="text-xs text-gray-500 mt-1">FCFA</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bénéfices</p>
                    <p class="text-xl sm:text-2xl font-bold text-emerald-600 mt-2 truncate">{{ number_format($stats['profit'] ?? 0, 0, ',', ' ') }}</p>
                    <p class="text-xs text-emerald-600 mt-1">FCFA</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                    <i data-lucide="dollar-sign" class="w-5 h-5 text-white"></i>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Liste des ventes --}}
    @if($sales->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-12 text-center shadow-sm">
            <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="shopping-cart" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Aucune vente enregistrée</h3>
            <p class="text-sm text-gray-500 mb-4">Commencez par enregistrer votre première vente</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-black text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouvelle vente
            </a>
        </div>
    @else
        {{-- Vue Desktop (Tableau) --}}
        <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendeur</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        @php
                                            $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                            $icon = $categoryIcons[$sale->product->productModel->category->value] ?? 'box';
                                        @endphp
                                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500 truncate">{{ $sale->product->productModel->brand }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if($sale->reseller)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg border border-amber-200 text-xs font-medium">
                                        <i data-lucide="store" class="w-3 h-3"></i>
                                        {{ $sale->reseller->name }}
                                    </span>
                                @elseif($sale->client_name)
                                    <div class="text-sm text-gray-900 truncate">{{ $sale->client_name }}</div>
                                    @if($sale->client_phone)
                                        <div class="text-xs text-gray-500 truncate">{{ $sale->client_phone }}</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">Client anonyme</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-medium flex-shrink-0">
                                        {{ substr($sale->seller->name ?? 'N', 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-900 truncate">{{ $sale->seller->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $sale->sale_type->value === 'achat_direct' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                    {{ $sale->sale_type->label() }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-semibold text-gray-900">{{ number_format($sale->prix_vente, 0, ',', ' ') }}</div>
                                <div class="text-xs text-gray-500">FCFA</div>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="text-sm text-gray-900">{{ $sale->date_vente_effective->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $sale->created_at->format('H:i') }}</div>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('sales.show', $sale) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 rounded-lg transition-all">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Détails
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sales->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>

        {{-- Vue Mobile/Tablet (Cards) --}}
        <div class="lg:hidden space-y-3">
            @foreach($sales as $sale)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all duration-200">
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-100">
                    @php
                        $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                        $icon = $categoryIcons[$sale->product->productModel->category->value] ?? 'box';
                    @endphp
                    <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6 text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ $sale->product->productModel->name }}</h4>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $sale->product->productModel->brand }}</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $sale->sale_type->value === 'achat_direct' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                                {{ $sale->sale_type->value === 'achat_direct' ? 'Direct' : 'Troc' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-base font-bold text-gray-900">{{ number_format($sale->prix_vente / 1000, 0) }}k</p>
                        <p class="text-xs text-gray-500">FCFA</p>
                    </div>
                </div>

                {{-- Info Grid --}}
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Client</p>
                            @if($sale->reseller)
                                <p class="text-sm font-medium text-amber-700 truncate">{{ $sale->reseller->name }}</p>
                            @else
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $sale->client_name ?: 'Anonyme' }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Vendeur</p>
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $sale->seller->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Date</p>
                            <p class="text-sm font-medium text-gray-900">{{ $sale->date_vente_effective->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-500">Heure</p>
                            <p class="text-sm font-medium text-gray-900">{{ $sale->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <a href="{{ route('sales.show', $sale) }}" 
                   class="flex items-center justify-center gap-2 w-full px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 bg-white hover:bg-gray-50 rounded-lg transition-all">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    Voir les détails
                </a>
            </div>
            @endforeach

            @if($sales->hasPages())
                <div class="px-4 py-3 bg-white border border-gray-200 rounded-xl">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    @endif
</x-app-layout>