<x-app-layout>
    <x-slot name="header">
        Profil Utilisateur : {{ $user->name }}
    </x-slot>

    <div class="space-y-6">
        {{-- En-tête avec statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Carte Info --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6 col-span-1 md:col-span-1">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xl font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h2>
                        <div class="mt-1">
                            @foreach($user->roles as $role)
                                @if($role->name === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200">
                                        <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                        Vendeur
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center text-sm text-gray-500">
                        <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                        Inscrit le {{ $user->created_at->format('d/m/Y') }}
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-100 pt-4 flex gap-2">
                    <a href="{{ route('users.edit', $user) }}" class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        <i data-lucide="edit" class="w-4 h-4 mr-2"></i>
                        Modifier
                    </a>
                </div>
            </div>

            {{-- Stats (si pertinent) --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6 col-span-1 md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 uppercase">Ventes Totales</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['total_sales'], 0, ',', ' ') }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-xs font-medium text-gray-500 uppercase">Chiffre d'Affaires</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    @if(auth()->user()->isAdmin())
                        <div class="p-4 bg-green-50 rounded-lg">
                            <p class="text-xs font-medium text-green-600 uppercase">Bénéfices Générés</p>
                            <p class="mt-2 text-2xl font-bold text-green-700">{{ number_format($stats['total_profit'], 0, ',', ' ') }} FCFA</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Dernières ventes --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Dernières ventes effectuées</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($user->sales as $sale)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->date_vente_effective->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $sale->product->productModel->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $sale->product->imei ?: $sale->product->serial_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $sale->client_name ?: ($sale->reseller ? $sale->reseller->name : '-') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($sale->is_confirmed)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                            Confirmé
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            En attente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Aucune vente enregistrée récemment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
