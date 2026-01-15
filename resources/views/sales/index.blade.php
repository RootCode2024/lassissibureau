<x-app-layout>
    <x-slot name="header">
        Ventes
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black border border-black rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nouvelle vente
        </a>
    </x-slot>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    {{-- Statistiques --}}
    <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Ventes du jour</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['today'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-gray-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Ventes du mois</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['month'] ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-gray-600"></i>
                </div>
            </div>
        </div>

        @if(auth()->user()->hasRole('admin'))
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">CA du mois</p>
                        <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['revenue'] ?? 0, 0, ',', ' ') }}</p>
                        <span class="text-xs text-gray-500">FCFA</span>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="coins" class="w-5 h-5 text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Bénéfices</p>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($stats['profit'] ?? 0, 0, ',', ' ') }}</p>
                        <span class="text-xs text-green-600">FCFA</span>
                    </div>
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Liste des ventes --}}
    @if($sales->isEmpty())
        <div class="bg-white border border-gray-200 rounded-lg p-12">
            <div class="text-center">
                <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="shopping-cart" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900 mb-1">Aucune vente enregistrée</h3>
                <p class="text-xs text-gray-500 mb-4">Commencez par enregistrer votre première vente</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black hover:bg-gray-800 text-white text-sm font-medium rounded-md transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Nouvelle vente
                </a>
            </div>
        </div>
    @else
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($sales as $sale)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            @php
                                                $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                                                $icon = $categoryIcons[$sale->product->productModel->category] ?? 'box';
                                            @endphp
                                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-gray-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $sale->product->productModel->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $sale->product->productModel->brand }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    @if($sale->client_name)
                                        <div class="text-sm text-gray-900">{{ $sale->client_name }}</div>
                                        @if($sale->client_phone)
                                            <div class="text-xs text-gray-500">{{ $sale->client_phone }}</div>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400">Client anonyme</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $sale->sale_type->value === 'achat_direct' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
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
                                    <a href="{{ route('sales.show', $sale) }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 rounded-md transition-colors">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        Voir
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
    @endif
</x-app-layout>
