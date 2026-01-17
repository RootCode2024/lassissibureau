<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-base sm:text-lg lg:text-xl text-gray-900 truncate">Profil : {{ $user->name }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        {{-- En-tête avec statistiques --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            {{-- Carte Info --}}
            <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4 mb-4">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-gray-900 to-gray-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <span class="text-white text-xl sm:text-2xl font-black">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div class="text-center sm:text-left min-w-0 flex-1">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-900 truncate">{{ $user->name }}</h2>
                        <div class="mt-1.5 flex justify-center sm:justify-start">
                            @foreach($user->roles as $role)
                                @if($role->name === 'admin')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                        <i data-lucide="shield" class="w-3 h-3"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        <i data-lucide="user" class="w-3 h-3"></i>
                                        Vendeur
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5 sm:space-y-3">
                    <div class="flex items-center gap-2 text-sm text-gray-600 p-2 bg-white rounded-lg border border-gray-100">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span class="truncate">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 p-2 bg-white rounded-lg border border-gray-100">
                        <i data-lucide="calendar" class="w-4 h-4 text-gray-400 flex-shrink-0"></i>
                        <span>Inscrit le {{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>

                <div class="mt-4 sm:mt-6 pt-4 border-t border-gray-200">
                    <a href="{{ route('users.edit', $user) }}" class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 active:bg-gray-950 transition-all hover:shadow-lg">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                        <span>Modifier le profil</span>
                    </a>
                </div>
            </div>

            {{-- Stats Performance --}}
            <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 lg:col-span-2 shadow-sm">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4 flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5 text-gray-600"></i>
                    Performance
                </h3>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl">
                        <p class="text-[10px] sm:text-xs font-semibold text-blue-600 uppercase tracking-wide">Ventes Totales</p>
                        <p class="mt-1 sm:mt-2 text-xl sm:text-2xl font-black text-blue-700">{{ number_format($stats['total_sales'], 0, ',', ' ') }}</p>
                    </div>
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl">
                        <p class="text-[10px] sm:text-xs font-semibold text-green-600 uppercase tracking-wide">CA</p>
                        <p class="mt-1 sm:mt-2 text-base sm:text-xl lg:text-2xl font-black text-green-700 break-all leading-tight">{{ number_format($stats['total_revenue'], 0, ',', ' ') }}</p>
                        <p class="text-[10px] sm:text-xs text-green-600 font-medium mt-0.5">FCFA</p>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <div class="p-3 sm:p-4 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl col-span-2 lg:col-span-1">
                            <p class="text-[10px] sm:text-xs font-semibold text-purple-600 uppercase tracking-wide">Bénéfices</p>
                            <p class="mt-1 sm:mt-2 text-base sm:text-xl lg:text-2xl font-black text-purple-700 break-all leading-tight">{{ number_format($stats['total_profit'], 0, ',', ' ') }}</p>
                            <p class="text-[10px] sm:text-xs text-purple-600 font-medium mt-0.5">FCFA</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Dernières ventes --}}
        {{-- Vue Desktop (Tableau) --}}
        <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-base sm:text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-gray-600"></i>
                    Dernières ventes effectuées
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produit</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                            <th scope="col" class="px-4 xl:px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($user->sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $sale->date_vente_effective->format('d/m/Y') }}
                                </td>
                                <td class="px-4 xl:px-6 py-4">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono truncate">{{ $sale->product->imei ?: $sale->product->serial_number }}</div>
                                    </div>
                                </td>
                                <td class="px-4 xl:px-6 py-4">
                                    <span class="text-sm text-gray-900 truncate block max-w-xs">{{ $sale->client_name ?: ($sale->reseller ? $sale->reseller->name : '-') }}</span>
                                </td>
                                <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-center">
                                    @if($sale->is_confirmed)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Confirmé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                            En attente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mb-3">
                                            <i data-lucide="shopping-bag" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">Aucune vente</p>
                                        <p class="text-xs text-gray-500 mt-1">Les ventes apparaîtront ici</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Vue Mobile/Tablet (Cards) --}}
        <div class="lg:hidden">
            <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 mb-3 shadow-sm">
                <h3 class="text-sm sm:text-base font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600"></i>
                    Dernières ventes ({{ $user->sales->count() }})
                </h3>
            </div>

            @forelse($user->sales as $sale)
                <div class="bg-white border border-gray-200 rounded-xl p-4 mb-3 shadow-sm">
                    <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-gray-100">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                            <p class="text-xs text-gray-500 font-mono truncate mt-0.5">{{ $sale->product->imei ?: $sale->product->serial_number }}</p>
                        </div>
                        @if($sale->is_confirmed)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800 border border-green-200 flex-shrink-0">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                OK
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-200 flex-shrink-0">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                Attente
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                            <span class="text-xs text-gray-600">{{ $sale->date_vente_effective->format('d/m/Y') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                            <span class="text-xs text-gray-600 truncate">{{ $sale->client_name ?: ($sale->reseller ? $sale->reseller->name : 'Client direct') }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <i data-lucide="coins" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0"></i>
                            <span class="text-sm font-bold text-gray-900">{{ number_format($sale->prix_vente / 1000, 0) }}k FCFA</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                    <div class="w-12 h-12 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="shopping-bag" class="w-6 h-6 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900">Aucune vente</p>
                    <p class="text-xs text-gray-500 mt-1">Les ventes apparaîtront ici</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>