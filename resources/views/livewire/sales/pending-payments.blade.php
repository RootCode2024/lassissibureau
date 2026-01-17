<div>
    {{-- Statistiques --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-6">
        <div class="bg-gradient-to-br from-white to-red-50 border border-red-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-semibold text-red-600 uppercase tracking-wide mb-1">Non payé</p>
                    <p class="text-base sm:text-xl lg:text-2xl font-black text-red-700 break-all leading-tight">{{ number_format($stats['total_unpaid'], 0, ',', ' ') }}</p>
                    <p class="text-[10px] sm:text-xs text-red-600 font-medium mt-0.5">FCFA</p>
                </div>
                <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-red-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i data-lucide="x-circle" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-white to-amber-50 border border-amber-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1">Partiel</p>
                    <p class="text-base sm:text-xl lg:text-2xl font-black text-amber-700 break-all leading-tight">{{ number_format($stats['total_partial'], 0, ',', ' ') }}</p>
                    <p class="text-[10px] sm:text-xs text-amber-600 font-medium mt-0.5">FCFA</p>
                </div>
                <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-white to-purple-50 border border-purple-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">En retard</p>
                    <p class="text-base sm:text-xl lg:text-2xl font-black text-purple-700 leading-tight">{{ $stats['overdue_count'] }}</p>
                    <p class="text-[10px] sm:text-xs text-purple-600 font-medium mt-0.5">ventes</p>
                </div>
                <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i data-lucide="alert-triangle" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-white to-purple-50 border border-purple-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <p class="text-[10px] sm:text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">Montant retard</p>
                    <p class="text-base sm:text-xl lg:text-2xl font-black text-purple-700 break-all leading-tight">{{ number_format($stats['overdue_amount'], 0, ',', ' ') }}</p>
                    <p class="text-[10px] sm:text-xs text-purple-600 font-medium mt-0.5">FCFA</p>
                </div>
                <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                    <i data-lucide="alert-circle" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 mb-4 sm:mb-6 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-4">
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
                <select wire:model.live="status_filter" id="status_filter" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                    <option value="all">Tous</option>
                    <option value="unpaid">Non payé</option>
                    <option value="partial">Partiellement payé</option>
                </select>
            </div>

            <div>
                <label for="sort_by" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                    Trier par
                </label>
                <select wire:model.live="sort_by" id="sort_by" class="block w-full py-2 sm:py-2.5 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                    <option value="due_date">Date d'échéance</option>
                    <option value="amount">Montant restant</option>
                    <option value="reseller">Revendeur</option>
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
                            Vente
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Revendeur
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Statut
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Prix total
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Payé
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Restant
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Échéance
                        </th>
                        <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 transition-colors {{ $sale->isPaymentOverdue() ? 'bg-red-50' : '' }}" wire:key="sale-desktop-{{ $sale->id }}">
                        <td class="px-4 xl:px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $sale->product->productModel->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $sale->date_vente_effective->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 xl:px-6 py-4">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $sale->reseller->name }}</p>
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $sale->payment_status->badgeClasses() }}">
                                <i data-lucide="{{ $sale->payment_status->icon() }}" class="w-3 h-3"></i>
                                {{ $sale->payment_status->label() }}
                            </span>
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            {{ number_format($sale->amount_paid, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                            {{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm">
                                <p class="text-gray-900">{{ $sale->payment_due_date->format('d/m/Y') }}</p>
                                @if($sale->isPaymentOverdue())
                                    <p class="text-xs font-medium text-red-600">En retard</p>
                                @else
                                    <p class="text-xs text-gray-500">{{ $sale->payment_due_date->diffForHumans() }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('sales.show', $sale) }}" class="text-gray-600 hover:text-gray-900 transition-colors" title="Voir détails">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <button wire:click="openQuickPay({{ $sale->id }})" class="text-green-600 hover:text-green-900 transition-colors" title="Paiement rapide">
                                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mb-3">
                                    <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">Aucun paiement en attente</p>
                                <p class="text-xs text-gray-500 mt-1">Tous les paiements sont à jour</p>
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
            <div class="bg-white border-2 rounded-xl p-4 shadow-sm hover:shadow-md transition-all {{ $sale->isPaymentOverdue() ? 'border-red-300 bg-red-50' : 'border-gray-200' }}" wire:key="sale-mobile-{{ $sale->id }}">
                {{-- Header --}}
                <div class="flex items-start gap-3 mb-3 pb-3 border-b border-gray-100">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="smartphone" class="w-5 h-5 text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $sale->date_vente_effective->format('d/m/Y') }}</p>
                        <p class="text-xs text-gray-600 mt-1 truncate">
                            <i data-lucide="store" class="w-3 h-3 inline"></i>
                            {{ $sale->reseller->name }}
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium border flex-shrink-0 {{ $sale->payment_status->badgeClasses() }}">
                        <i data-lucide="{{ $sale->payment_status->icon() }}" class="w-3 h-3"></i>
                        <span class="hidden sm:inline">{{ $sale->payment_status->label() }}</span>
                    </span>
                </div>

                {{-- Info Grid --}}
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="text-center p-2 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-[10px] text-gray-500 mb-0.5">Prix total</p>
                        <p class="text-xs font-bold text-gray-900">{{ number_format($sale->prix_vente / 1000, 0) }}k</p>
                    </div>
                    <div class="text-center p-2 bg-green-50 rounded-lg border border-green-100">
                        <p class="text-[10px] text-green-600 mb-0.5">Payé</p>
                        <p class="text-xs font-bold text-green-700">{{ number_format($sale->amount_paid / 1000, 0) }}k</p>
                    </div>
                    <div class="text-center p-2 bg-red-50 rounded-lg border border-red-100">
                        <p class="text-[10px] text-red-600 mb-0.5">Reste</p>
                        <p class="text-xs font-bold text-red-700">{{ number_format($sale->amount_remaining / 1000, 0) }}k</p>
                    </div>
                </div>

                {{-- Échéance --}}
                <div class="mb-3 p-2.5 {{ $sale->isPaymentOverdue() ? 'bg-red-100 border border-red-200' : 'bg-gray-50 border border-gray-200' }} rounded-lg">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 {{ $sale->isPaymentOverdue() ? 'text-red-600' : 'text-gray-600' }}"></i>
                            <span class="text-xs font-medium {{ $sale->isPaymentOverdue() ? 'text-red-900' : 'text-gray-900' }}">
                                {{ $sale->payment_due_date->format('d/m/Y') }}
                            </span>
                        </div>
                        @if($sale->isPaymentOverdue())
                            <span class="text-xs font-semibold text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                En retard
                            </span>
                        @else
                            <span class="text-xs text-gray-500">{{ $sale->payment_due_date->diffForHumans() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2">
                    <a href="{{ route('sales.show', $sale) }}" class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition-colors">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Voir</span>
                    </a>
                    <button wire:click="openQuickPay({{ $sale->id }})" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-green-600 border border-green-600 rounded-lg hover:bg-green-700 active:bg-green-800 transition-colors">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Payer</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center shadow-sm">
                <div class="w-12 h-12 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Aucun paiement en attente</p>
                <p class="text-xs text-gray-500 mt-1">Tous les paiements sont à jour</p>
            </div>
        @endforelse

        @if($sales->hasPages())
            <div class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg">
                {{ $sales->links() }}
            </div>
        @endif
    </div>

    {{-- Modale Paiement Rapide --}}
    @if($showQuickPayModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeQuickPayModal"></div>

                <div class="relative bg-white rounded-xl shadow-xl transform transition-all w-full max-w-lg">
                    <livewire:sales.quick-pay :saleId="$selectedSaleId" :key="$selectedSaleId" />
                </div>
            </div>
        </div>
    @endif
</div>