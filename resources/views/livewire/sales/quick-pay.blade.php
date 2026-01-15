<div class="p-6">
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900">Enregistrer un paiement</h3>
        <p class="text-sm text-gray-500">Ajouter un règlement pour cette vente.</p>
    </div>

    <form wire:submit.prevent="save">
        <div class="space-y-4">
            {{-- Montant --}}
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">Montant (FCFA)</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="number" wire:model="amount" id="amount" class="focus:ring-gray-900 focus:border-gray-900 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="0">
                </div>
                @error('amount') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Mode de paiement --}}
            <div>
                <label for="paymentMethod" class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                <select wire:model="paymentMethod" id="paymentMethod" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
                    <option value="cash">Espèces</option>
                    <option value="mobile_money">Mobile Money</option>
                    <option value="bank_transfer">Virement</option>
                    <option value="cheque">Chèque</option>
                </select>
                @error('paymentMethod') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optionnel)</label>
                <textarea wire:model="notes" id="notes" rows="2" class="shadow-sm focus:ring-gray-900 focus:border-gray-900 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"></textarea>
                @error('notes') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" wire:click="$parent.closeQuickPayModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Annuler
            </button>
            <button type="submit" class="inline-flex justify-center px-4 py-2 bg-gray-900 border border-transparent rounded-md text-sm font-medium text-white hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
                Valider le paiement
            </button>
        </div>
    </form>
</div>
