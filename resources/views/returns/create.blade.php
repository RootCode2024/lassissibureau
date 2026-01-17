<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">Nouveau Retour Client</h2>
                <p class="text-sm text-gray-500 mt-1">Gérez les retours et échanges de produits</p>
            </div>
        </div>
    </x-slot>

    @if(!$sale)
        {{-- ÉTAPE 1 : CHOIX DE LA VENTE --}}
        <div class="max-w-5xl mx-auto">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rechercher une vente</h3>
                <form action="{{ route('returns.create') }}" method="GET" class="flex gap-4">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Nom client, IMEI ou ID vente..." 
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10"
                    />
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors">
                        Rechercher
                    </button>
                </form>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-900">Ventes récentes éligibles au retour</h3>
                    <p class="text-sm text-gray-500 mt-1">Seules les ventes confirmées et livrées sont affichées</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit / IMEI</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentSales as $recentSale)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $recentSale->date_vente_effective->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $recentSale->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500 font-mono">{{ $recentSale->product->imei ?: $recentSale->product->serial_number }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $recentSale->client_name ?: 'Client de passage' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                                        {{ number_format($recentSale->prix_vente, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a 
                                            href="{{ route('returns.create', ['sale_id' => $recentSale->id]) }}" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors">
                                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                            Sélectionner
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <i data-lucide="inbox" class="w-12 h-12 text-gray-400"></i>
                                            <p class="text-sm text-gray-500">Aucune vente récente éligible trouvée</p>
                                        </div>
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
            {{-- Vente sélectionnée --}}
            <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 border border-blue-200 rounded-xl p-4 mb-6 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="shopping-bag" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-blue-900">Vente #{{ $sale->id }} sélectionnée</h3>
                        <p class="text-sm text-blue-700">{{ $sale->product->productModel->name }} - {{ $sale->client_name ?: 'Client de passage' }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">{{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <a 
                    href="{{ route('returns.create') }}" 
                    class="text-sm text-blue-700 hover:text-blue-900 underline">
                    Changer
                </a>
            </div>

            {{-- Affichage des erreurs --}}
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-red-900 mb-2">Erreurs de validation</h4>
                            <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('returns.store') }}" class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm space-y-6">
                @csrf
                
                {{-- Champs cachés --}}
                <input type="hidden" name="original_sale_id" value="{{ $sale->id }}">
                <input type="hidden" name="returned_product_id" value="{{ $sale->product_id }}">

                {{-- Motif du retour --}}
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motif du retour <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        name="reason" 
                        id="reason" 
                        rows="4" 
                        required 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 text-sm @error('reason') border-red-300 @enderror" 
                        placeholder="Décrivez la raison du retour (minimum 10 caractères)...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description du défaut (optionnel) --}}
                <div>
                    <label for="defect_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description du défaut (optionnel)
                    </label>
                    <textarea 
                        name="defect_description" 
                        id="defect_description" 
                        rows="3" 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 text-sm" 
                        placeholder="Détails techniques du problème constaté...">{{ old('defect_description') }}</textarea>
                </div>

                {{-- Type de résolution --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Type de résolution <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors @error('is_exchange') border-red-300 @else border-gray-200 @enderror">
                            <input 
                                type="radio" 
                                name="is_exchange" 
                                value="0" 
                                class="mt-0.5 h-4 w-4 text-gray-900 focus:ring-gray-900" 
                                {{ old('is_exchange', '0') == '0' ? 'checked' : '' }}
                                onchange="toggleExchangeFields(false)"
                            />
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Remboursement / Avoir</span>
                                <span class="block text-sm text-gray-500 mt-1">Le client sera remboursé du montant de la vente</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors @error('is_exchange') border-red-300 @else border-gray-200 @enderror">
                            <input 
                                type="radio" 
                                name="is_exchange" 
                                value="1" 
                                class="mt-0.5 h-4 w-4 text-gray-900 focus:ring-gray-900"
                                {{ old('is_exchange') == '1' ? 'checked' : '' }}
                                onchange="toggleExchangeFields(true)"
                            />
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-medium text-gray-900">Échange contre un autre produit</span>
                                <span class="block text-sm text-gray-500 mt-1">Le client reçoit un produit de remplacement</span>
                            </div>
                        </label>
                    </div>
                    @error('is_exchange')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Sélection produit d'échange --}}
                <div id="exchange-fields" style="display: {{ old('is_exchange') == '1' ? 'block' : 'none' }};">
                    <label for="exchange_product_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Produit de remplacement <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="exchange_product_id" 
                        id="exchange_product_id" 
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 text-sm @error('exchange_product_id') border-red-300 @enderror">
                        <option value="">-- Sélectionner le produit de remplacement --</option>
                        @foreach($availableProducts as $product)
                            <option 
                                value="{{ $product->id }}"
                                {{ old('exchange_product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->productModel->name }} - {{ $product->imei ?: $product->serial_number }} ({{ number_format($product->prix_vente, 0, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                    @error('exchange_product_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <i data-lucide="info" class="w-3 h-3 inline"></i>
                        Seuls les produits disponibles en stock sont affichés
                    </p>
                </div>

                {{-- Montant du remboursement --}}
                <div id="refund-fields" style="display: {{ old('is_exchange', '0') == '0' ? 'block' : 'none' }};">
                    <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Montant du remboursement <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input 
                            type="number" 
                            name="refund_amount" 
                            id="refund_amount" 
                            min="0" 
                            max="{{ $sale->prix_vente }}" 
                            step="1" 
                            value="{{ old('refund_amount', $sale->prix_vente) }}"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-2 focus:ring-gray-900/10 text-sm pr-20 @error('refund_amount') border-red-300 @enderror"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-sm text-gray-500 font-medium">FCFA</span>
                        </div>
                    </div>
                    @error('refund_amount')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        Maximum : {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA (prix de vente original)
                    </p>
                </div>

                {{-- Actions --}}
                <div class="border-t border-gray-200 pt-6 flex flex-col-reverse sm:flex-row justify-end gap-3">
                    <a 
                        href="{{ route('returns.index') }}" 
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Annuler
                    </a>
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-gray-800 transition-colors shadow-sm">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Valider le retour
                    </button>
                </div>
            </form>
        </div>
    @endif

    <script>
        function toggleExchangeFields(isExchange) {
            const exchangeFields = document.getElementById('exchange-fields');
            const refundFields = document.getElementById('refund-fields');
            const exchangeSelect = document.getElementById('exchange_product_id');
            const refundInput = document.getElementById('refund_amount');

            if (isExchange) {
                exchangeFields.style.display = 'block';
                refundFields.style.display = 'none';
                exchangeSelect.required = true;
                refundInput.required = false;
                refundInput.value = '0';
            } else {
                exchangeFields.style.display = 'none';
                refundFields.style.display = 'block';
                exchangeSelect.required = false;
                refundInput.required = true;
                refundInput.value = '{{ $sale->prix_vente ?? 0 }}';
            }
        }

        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const isExchangeChecked = document.querySelector('input[name="is_exchange"]:checked');
            if (isExchangeChecked) {
                toggleExchangeFields(isExchangeChecked.value === '1');
            }
        });
    </script>
</x-app-layout>