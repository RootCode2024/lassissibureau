<x-app-layout>
    <x-slot name="header">
        Nouveau Retour Client
    </x-slot>

    @if(!$sale)
        {{-- ÉTAPE 1 : CHOIX DE LA VENTE --}}
        <div class="max-w-4xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rechercher une vente</h3>
                <form action="{{ route('returns.create') }}" method="GET" class="flex gap-4">
                    <input type="text" name="search" placeholder="Nom client, IMEI ou ID vente..." class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900">
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                        Rechercher
                    </button>
                    {{-- TODO: Implémenter la recherche côté contrôleur si nécessaire, ici on utilise recentSales --}}
                </form>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-medium text-gray-900">Ventes récentes éligibles au retour</h3>
                    <p class="text-sm text-gray-500">Seules les ventes confirmées sont affichées.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit / IMEI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentSales as $recentSale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $recentSale->date_vente_effective->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $recentSale->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $recentSale->product->imei ?: $recentSale->product->serial_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $recentSale->client_name ?: 'Client de passage' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('returns.create', ['sale_id' => $recentSale->id]) }}" class="text-blue-600 hover:text-blue-900">
                                            Sélectionner
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        Aucune vente récente éligible trouvée.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        {{-- ÉTAPE 2 : FORMULAIRE DE RETOUR --}}
        <div class="max-w-3xl mx-auto">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6 flex justify-between items-center">
                <div>
                    <h3 class="font-medium text-gray-900">Vente sélectionnée : #{{ $sale->id }}</h3>
                    <p class="text-sm text-gray-500">{{ $sale->product->productModel->name }} - {{ $sale->client_name }}</p>
                </div>
                <a href="{{ route('returns.create') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">Changer de vente</a>
            </div>

            <form method="POST" action="{{ route('returns.store') }}" class="bg-white border border-gray-200 rounded-lg p-6 space-y-6" x-data="{ isExchange: false }">
                @csrf
                <input type="hidden" name="original_sale_id" value="{{ $sale->id }}">
                <input type="hidden" name="returned_product_id" value="{{ $sale->product_id }}">
                <input type="hidden" name="processed_by" value="{{ auth()->id() }}">

                {{-- Motif --}}
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif du retour</label>
                    <textarea name="reason" id="reason" rows="3" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm" placeholder="Ex: Défaut technique, client insatisfait..."></textarea>
                </div>

                {{-- Type de retour --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type de résolution</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_exchange" value="0" x-model="isExchange" class="form-radio text-gray-900 focus:ring-gray-900">
                            <span class="ml-2 text-sm text-gray-700">Remboursement / Avoir</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="is_exchange" value="1" x-model="isExchange" class="form-radio text-gray-900 focus:ring-gray-900">
                            <span class="ml-2 text-sm text-gray-700">Échange contre un autre produit</span>
                        </label>
                    </div>
                </div>

                {{-- Sélection produit échange --}}
                <div x-show="isExchange" style="display: none;">
                    <label for="exchange_product_id" class="block text-sm font-medium text-gray-700 mb-1">Produit de remplacement</label>
                    <select name="exchange_product_id" id="exchange_product_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 sm:text-sm">
                        <option value="">-- Choisir le produit --</option>
                        @foreach($availableProducts as $product)
                            <option value="{{ $product->id }}">
                                {{ $product->productModel->name }} - {{ $product->imei ?: $product->serial_number }} ({{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Seuls les produits disponibles en stock sont affichés.</p>
                </div>

                <div class="border-t border-gray-200 pt-6 flex justify-end gap-3">
                    <a href="{{ route('returns.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                        Valider le retour
                    </button>
                </div>
            </form>
        </div>
    @endif
</x-app-layout>
