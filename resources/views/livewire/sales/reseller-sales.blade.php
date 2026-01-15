<div>
            {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En attente</p>
                        <p class="text-2xl font-bold text-amber-600 mt-2">{{ $stats['pending_count'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($stats['pending_value'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Confirmées</p>
                        <p class="text-2xl font-bold text-green-600 mt-2">{{ $stats['confirmed_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Valeur totale</p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">{{ number_format($stats['pending_value'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-gray-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="reseller_filter" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Revendeur
                    </label>
                    <select wire:model.live="reseller_id" id="reseller_filter" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Tous les revendeurs</option>
                        @foreach($resellers as $reseller)
                            <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status_filter" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Statut
                    </label>
                    <select wire:model.live="status" id="status_filter" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Tous</option>
                        <option value="pending">En attente</option>
                        <option value="confirmed">Confirmées</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Liste des ventes --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Produit
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Revendeur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date dépôt
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Paiement
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $sale->product->productModel->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $sale->product->productModel->brand }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-900">{{ $sale->reseller->name }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $sale->date_depot_revendeur->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <p class="font-semibold text-green-600">{{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</p>
                                    @if($sale->amount_remaining > 0)
                                        <p class="text-xs text-red-600">Reste: {{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($sale->is_confirmed)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        Confirmée
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        En attente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-gray-600 hover:text-gray-900" title="Voir détails">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if(!$sale->is_confirmed)
                                        <button wire:click="openConfirmModal({{ $sale->id }})" class="text-green-600 hover:text-green-900" title="Confirmer">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                        <button wire:click="openReturnModal({{ $sale->id }})" class="text-red-600 hover:text-red-900" title="Retourner">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-900">Aucune vente revendeur</p>
                                    <p class="text-xs text-gray-500 mt-1">Les ventes revendeurs apparaîtront ici</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($sales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $sales->links() }}
            </div>
            @endif
        </div>

        {{-- Modal de confirmation --}}
        @if($showConfirmModal && $selectedSale)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfirmModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="confirmSale">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmer la vente</h3>
                                    <div class="mt-4 space-y-4">
                                        <p class="text-sm text-gray-500">
                                            Confirmer la vente de <strong>{{ $selectedSale->product->productModel->name }}</strong> à <strong>{{ $selectedSale->reseller->name }}</strong> ?
                                        </p>

                                        @if($selectedSale->amount_remaining > 0)
                                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                            <p class="text-xs text-amber-800 mb-2">Solde restant: <strong>{{ number_format($selectedSale->amount_remaining, 0, ',', ' ') }} FCFA</strong></p>
                                            <div>
                                                <label for="confirmPaymentAmount" class="block text-xs font-medium text-gray-700 mb-1">
                                                    Montant payé (optionnel)
                                                </label>
                                                <input wire:model="confirmPaymentAmount" type="number" id="confirmPaymentAmount" min="0" step="0.01" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                            </div>
                                            <div class="mt-2">
                                                <label for="confirmPaymentMethod" class="block text-xs font-medium text-gray-700 mb-1">
                                                    Méthode de paiement
                                                </label>
                                                <select wire:model="confirmPaymentMethod" id="confirmPaymentMethod" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                                    <option value="cash">Espèces</option>
                                                    <option value="mobile_money">Mobile Money</option>
                                                    <option value="bank_transfer">Virement</option>
                                                    <option value="check">Chèque</option>
                                                </select>
                                            </div>
                                        </div>
                                        @endif

                                        <div>
                                            <label for="confirmNotes" class="block text-sm font-medium text-gray-700 mb-1">
                                                Notes (optionnel)
                                            </label>
                                            <textarea wire:model="confirmNotes" id="confirmNotes" rows="2" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                                Confirmer
                            </button>
                            <button type="button" wire:click="closeConfirmModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- Modal de retour --}}
        @if($showReturnModal && $selectedSale)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeReturnModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="returnProduct">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i data-lucide="arrow-left-circle" class="w-6 h-6 text-red-600"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Retourner le produit</h3>
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-500 mb-4">
                                            Retourner <strong>{{ $selectedSale->product->productModel->name }}</strong> en stock ?
                                        </p>
                                        <div>
                                            <label for="returnReason" class="block text-sm font-medium text-gray-700 mb-1">
                                                Motif du retour *
                                            </label>
                                            <textarea wire:model="returnReason" id="returnReason" rows="3" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Expliquez pourquoi le produit est retourné..." required></textarea>
                                            @error('returnReason') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm" wire:loading.attr="disabled">
                                Retourner
                            </button>
                            <button type="button" wire:click="closeReturnModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

</div>
