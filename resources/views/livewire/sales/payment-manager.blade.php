<div>
    {{-- Bouton pour ouvrir le modal --}}
    @if(!$sale->isFullyPaid())
    <button wire:click="openModal" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 border border-green-600 rounded-md font-medium text-sm text-white hover:bg-green-700 transition-colors">
        <i data-lucide="plus-circle" class="w-4 h-4"></i>
        Enregistrer un paiement
    </button>
    @endif

    {{-- Historique des paiements --}}
    @if($payments->isNotEmpty())
    <div class="mt-6 bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Historique des paiements</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Méthode</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enregistré par</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($payments as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->payment_date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i data-lucide="{{ $payment->payment_method->icon() }}" class="w-3 h-3"></i>
                                {{ $payment->payment_method->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $payment->recorder->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $payment->notes ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Modal d'enregistrement de paiement --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            {{-- Center modal --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal panel --}}
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form wire:submit="recordPayment">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i data-lucide="credit-card" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Enregistrer un paiement
                                </h3>
                                <div class="mt-4 space-y-4">
                                    {{-- Solde restant --}}
                                    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-xs text-blue-600 font-medium mb-1">Solde restant</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ number_format($sale->amount_remaining, 0, ',', ' ') }} FCFA</p>
                                    </div>

                                    {{-- Montant --}}
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                                            Montant du paiement *
                                        </label>
                                        <div class="relative">
                                            <input wire:model="amount" type="number" id="amount" min="0.01" step="0.01" class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="0">
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <span class="text-xs text-gray-500 font-medium">FCFA</span>
                                            </div>
                                        </div>
                                        @error('amount') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Méthode de paiement --}}
                                    <div>
                                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">
                                            Méthode de paiement *
                                        </label>
                                        <select wire:model="payment_method" id="payment_method" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                            <option value="cash">Espèces</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="bank_transfer">Virement bancaire</option>
                                            <option value="check">Chèque</option>
                                        </select>
                                        @error('payment_method') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Date --}}
                                    <div>
                                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">
                                            Date du paiement *
                                        </label>
                                        <input wire:model="payment_date" type="date" id="payment_date" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                                        @error('payment_date') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>

                                    {{-- Notes --}}
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                            Notes (optionnel)
                                        </label>
                                        <textarea wire:model="notes" id="notes" rows="2" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Notes sur ce paiement..."></textarea>
                                        @error('notes') <span class="mt-1 text-xs text-red-600">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                        <button type="submit" class="w-full inline-flex justify-center items-center gap-2 rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="recordPayment">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </span>
                            <span wire:loading wire:target="recordPayment">
                                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span wire:loading.remove wire:target="recordPayment">Enregistrer</span>
                            <span wire:loading wire:target="recordPayment">Enregistrement...</span>
                        </button>
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Notification de succès --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-4 right-4 z-50 p-4 bg-green-50 border border-green-200 rounded-lg shadow-lg">
            <div class="flex items-center gap-3">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif
</div>
