<div>
    {{-- Statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En attente</p>
                    <p class="text-xl sm:text-2xl font-bold text-amber-600 mt-1 sm:mt-2">{{ $stats['pending_count'] }}</p>
                    <p class="text-xs text-gray-500 mt-1 break-all">{{ number_format($stats['pending_value'], 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="clock" class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Confirmées</p>
                    <p class="text-xl sm:text-2xl font-bold text-green-600 mt-1 sm:mt-2">{{ $stats['confirmed_count'] }}</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Valeur totale</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1 sm:mt-2 break-all">{{ number_format($stats['pending_value'], 0, ',', ' ') }} FCFA</p>
                </div>
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
            <div>
                <label for="reseller_filter" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                    Revendeur
                </label>
                <select wire:model.live="reseller_id" id="reseller_filter" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                    <option value="">Tous les revendeurs</option>
                    @foreach($resellers as $reseller)
                        <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status_filter" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                    Statut
                </label>
                <select wire:model.live="status" id="status_filter" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                    <option value="">Tous</option>
                    <option value="pending">En attente</option>
                    <option value="confirmed">Confirmées</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Vue Desktop (Tableau) --}}
    <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Produit
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Revendeur
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Date dépôt
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Prix
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Paiement
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Statut
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 transition-colors" wire:key="sale-desktop-{{ $sale->id }}">
                        <td class="px-4 xl:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $sale->product->productModel->brand }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 xl:px-6 py-4">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $sale->reseller->name }}</p>
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $sale->date_depot_revendeur->format('d/m/Y') }}
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="font-semibold text-green-600">{{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA</p>
                                @if($sale->amount_remaining > 0)
                                    <p class="text-xs text-red-600">Reste: {{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
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
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sales.show', $sale) }}" class="text-gray-600 hover:text-gray-900 transition-colors" title="Voir détails">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                @if(!$sale->is_confirmed)
                                    <button wire:click="openConfirmModal({{ $sale->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Confirmer">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                    <button wire:click="openReturnModal({{ $sale->id }})" class="text-red-600 hover:text-red-900 transition-colors" title="Retourner">
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
                                <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mb-3">
                                    <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">Aucune vente revendeur</p>
                                <p class="text-xs text-gray-500 mt-1">Les ventes revendeurs apparaîtront ici</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sales->hasPages())
        <div class="px-4 xl:px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $sales->links() }}
        </div>
        @endif
    </div>

    {{-- Vue Mobile/Tablet (Cards) --}}
    <div class="lg:hidden space-y-3">
        @forelse($sales as $sale)
            <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all" wire:key="sale-mobile-{{ $sale->id }}">
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ $sale->product->productModel->brand }}</p>
                        <p class="text-xs text-gray-600 mt-1 truncate">
                            <i data-lucide="store" class="w-3 h-3 inline"></i>
                            {{ $sale->reseller->name }}
                        </p>
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

                {{-- Info Grid --}}
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-500 mb-0.5">Date</p>
                        <p class="text-xs font-medium text-gray-900">{{ $sale->date_depot_revendeur->format('d/m') }}</p>
                    </div>
                    <div class="text-center p-2 bg-gray-50 rounded-lg">
                        <p class="text-[10px] text-gray-500 mb-0.5">Prix</p>
                        <p class="text-xs font-bold text-gray-900">{{ number_format($sale->prix_vente / 1000, 0) }}k</p>
                    </div>
                    <div class="text-center p-2 rounded-lg {{ $sale->amount_remaining > 0 ? 'bg-red-50' : 'bg-green-50' }}">
                        <p class="text-[10px] {{ $sale->amount_remaining > 0 ? 'text-red-600' : 'text-green-600' }} mb-0.5">Payé</p>
                        <p class="text-xs font-bold {{ $sale->amount_remaining > 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format($sale->amount_paid / 1000, 0) }}k</p>
                    </div>
                </div>

                @if($sale->amount_remaining > 0)
                    <div class="mb-3 p-2 bg-red-50 rounded-lg">
                        <p class="text-xs text-red-600 text-center">
                            Reste: <strong>{{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA</strong>
                        </p>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('sales.show', $sale) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Voir</span>
                    </a>
                    @if(!$sale->is_confirmed)
                        <button wire:click="openConfirmModal({{ $sale->id }})" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 active:bg-green-200 transition-colors" title="Confirmer">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </button>
                        <button wire:click="openReturnModal({{ $sale->id }})" class="inline-flex items-center justify-center px-3 py-2 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 active:bg-red-200 transition-colors" title="Retourner">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                <div class="w-12 h-12 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Aucune vente revendeur</p>
                <p class="text-xs text-gray-500 mt-1">Les ventes revendeurs apparaîtront ici</p>
            </div>
        @endforelse

        @if($sales->hasPages())
            <div class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de confirmation --}}
    @if($showConfirmModal && $selectedSale)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeConfirmModal"></div>
            <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-lg">
                <form wire:submit="confirmSale">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="check-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Confirmer la vente</h3>
                                <div class="mt-3 sm:mt-4 space-y-3 sm:space-y-4">
                                    <p class="text-sm text-gray-600">
                                        Confirmer la vente de <strong>{{ $selectedSale->product->productModel->name }}</strong> à <strong>{{ $selectedSale->reseller->name }}</strong> ?
                                    </p>

                                    @if($selectedSale->amount_remaining > 0)
                                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg space-y-3">
                                        <p class="text-xs text-amber-800">Solde restant: <strong>{{ number_format($selectedSale->amount_remaining, 0, ',', ' ') }} FCFA</strong></p>
                                        <div>
                                            <label for="confirmPaymentAmount" class="block text-xs font-medium text-gray-700 mb-1.5">
                                                Montant payé (optionnel)
                                            </label>
                                            <input wire:model="confirmPaymentAmount" type="number" id="confirmPaymentAmount" min="0" step="0.01" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                                        </div>
                                        <div>
                                            <label for="confirmPaymentMethod" class="block text-xs font-medium text-gray-700 mb-1.5">
                                                Méthode de paiement
                                            </label>
                                            <select wire:model="confirmPaymentMethod" id="confirmPaymentMethod" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                                                <option value="cash">Espèces</option>
                                                <option value="mobile_money">Mobile Money</option>
                                                <option value="bank_transfer">Virement</option>
                                                <option value="check">Chèque</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <div>
                                        <label for="confirmNotes" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                                            Notes (optionnel)
                                        </label>
                                        <textarea wire:model="confirmNotes" id="confirmNotes" rows="2" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 rounded-b-xl">
                        <button type="button" wire:click="closeConfirmModal" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg bg-green-600 text-sm font-medium text-white hover:bg-green-700 active:bg-green-800 transition-colors" wire:loading.attr="disabled">
                            Confirmer
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
        <div class="flex items-end sm:items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeReturnModal"></div>
            <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-lg">
                <form wire:submit="returnProduct">
                    <div class="p-4 sm:p-6">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i data-lucide="arrow-left-circle" class="w-5 h-5 sm:w-6 sm:h-6 text-red-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900">Retourner le produit</h3>
                                <div class="mt-3 sm:mt-4">
                                    <p class="text-sm text-gray-600 mb-3 sm:mb-4">
                                        Retourner <strong>{{ $selectedSale->product->productModel->name }}</strong> en stock ?
                                    </p>
                                    <div>
                                        <label for="returnReason" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5">
                                            Motif du retour *
                                        </label>
                                        <textarea wire:model="returnReason" id="returnReason" rows="3" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm" placeholder="Expliquez pourquoi le produit est retourné..." required></textarea>
                                        @error('returnReason') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3 rounded-b-xl">
                        <button type="button" wire:click="closeReturnModal" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors">
                            Annuler
                        </button>
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-lg bg-red-600 text-sm font-medium text-white hover:bg-red-700 active:bg-red-800 transition-colors" wire:loading.attr="disabled">
                            Retourner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>