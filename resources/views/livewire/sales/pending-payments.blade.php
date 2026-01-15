<div>
        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Non payé</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">{{ number_format($stats['total_unpaid'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Partiellement payé</p>
                        <p class="text-2xl font-bold text-amber-600 mt-2">{{ number_format($stats['total_partial'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En retard</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">{{ $stats['overdue_count'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Montant en retard</p>
                        <p class="text-2xl font-bold text-purple-600 mt-2">{{ number_format($stats['overdue_amount'], 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <select wire:model.live="status_filter" id="status_filter" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="all">Tous</option>
                        <option value="unpaid">Non payé</option>
                        <option value="partial">Partiellement payé</option>
                    </select>
                </div>

                <div>
                    <label for="sort_by" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Trier par
                    </label>
                    <select wire:model.live="sort_by" id="sort_by" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="due_date">Date d'échéance</option>
                        <option value="amount">Montant restant</option>
                        <option value="reseller">Revendeur</option>
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
                                Vente
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Revendeur
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Prix total
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payé
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Restant
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Échéance
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                        <tr class="hover:bg-gray-50 {{ $sale->isPaymentOverdue() ? 'bg-red-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $sale->product->productModel->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $sale->date_vente_effective->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm font-medium text-gray-900">{{ $sale->reseller->name }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $sale->payment_status->badgeClasses() }}">
                                    <i data-lucide="{{ $sale->payment_status->icon() }}" class="w-3 h-3"></i>
                                    {{ $sale->payment_status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                {{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                {{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    <p class="text-gray-900">{{ $sale->payment_due_date->format('d/m/Y') }}</p>
                                    @if($sale->isPaymentOverdue())
                                        <p class="text-xs font-medium text-red-600">En retard</p>
                                    @else
                                        <p class="text-xs text-gray-500">{{ $sale->payment_due_date->diffForHumans() }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('sales.show', $sale) }}" class="text-gray-600 hover:text-gray-900" title="Voir détails">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <button wire:click="openQuickPay({{ $sale->id }})" class="text-green-600 hover:text-green-900" title="Paiement rapide">
                                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mb-3"></i>
                                    <p class="text-sm font-medium text-gray-900">Aucun paiement en attente</p>
                                    <p class="text-xs text-gray-500 mt-1">Tous les paiements sont à jour</p>
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

        {{-- Modale Paiement Rapide --}}
        @if($showQuickPayModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeQuickPayModal"></div>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <livewire:sales.quick-pay :saleId="$selectedSaleId" :key="$selectedSaleId" />
                    </div>
                </div>
            </div>
        @endif
</div>
