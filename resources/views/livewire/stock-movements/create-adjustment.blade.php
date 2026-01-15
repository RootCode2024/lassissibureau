{{-- resources/views/stock-movements/create-adjustment.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        Ajustement de stock
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        @livewire('stock-movements.create-adjustment')
    </div>
</x-app-layout>

{{-- resources/views/livewire/stock-movements/create-adjustment.blade.php --}}
<div>
    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-sm text-red-800">{{ session('error') }}</p>
        </div>
    @endif

    <form wire:submit.prevent="submit" class="space-y-6">

        {{-- Sélection du produit --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                    <i data-lucide="package" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Produit concerné</h3>
                    <p class="text-xs text-gray-500">Sélectionnez le produit à ajuster</p>
                </div>
            </div>

            <div>
                <label for="product_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                    Produit *
                </label>
                <select wire:model="product_id" id="product_id" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->productModel->brand }} {{ $product->productModel->name }}
                            @if($product->imei) - IMEI: {{ $product->imei }} @endif
                            - {{ $product->state->label() }}
                        </option>
                    @endforeach
                </select>
                @error('product_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Type d'ajustement --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Type d'ajustement</h3>
                    <p class="text-xs text-gray-500">Raison de l'ajustement</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors @if($adjustment_type === 'correction') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 @endif">
                    <input type="radio" wire:model="adjustment_type" value="correction" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900">Correction inventaire</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Ajustement comptable</span>
                    </div>
                </label>

                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors @if($adjustment_type === 'casse') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 @endif">
                    <input type="radio" wire:model="adjustment_type" value="casse" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900">Casse</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Produit cassé/endommagé</span>
                    </div>
                </label>

                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors @if($adjustment_type === 'vol') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 @endif">
                    <input type="radio" wire:model="adjustment_type" value="vol" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900">Vol</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Produit volé</span>
                    </div>
                </label>

                <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer transition-colors @if($adjustment_type === 'perte') border-gray-900 bg-gray-50 @else border-gray-200 hover:border-gray-900 @endif">
                    <input type="radio" wire:model="adjustment_type" value="perte" class="mt-0.5 rounded-full border-gray-300 text-gray-900 focus:ring-gray-900">
                    <div class="ml-3">
                        <span class="block text-sm font-medium text-gray-900">Perte</span>
                        <span class="block text-xs text-gray-500 mt-0.5">Produit perdu/disparu</span>
                    </div>
                </label>
            </div>
            @error('adjustment_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Justification --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-600 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-warning" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Justification *</h3>
                    <p class="text-xs text-gray-500">Explication détaillée obligatoire</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="justification" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Détails de la justification *
                    </label>
                    <textarea wire:model="justification" id="justification" rows="4" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Expliquez en détail la raison de cet ajustement..."></textarea>
                    <p class="mt-1 text-xs text-gray-500">Cette information sera enregistrée dans l'historique</p>
                    @error('justification') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Notes complémentaires
                    </label>
                    <textarea wire:model="notes" id="notes" rows="2" class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm" placeholder="Informations additionnelles..."></textarea>
                    @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Avertissement --}}
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <h4 class="text-sm font-semibold text-amber-900">Action irréversible</h4>
                    <p class="text-xs text-amber-800 mt-1">Cet ajustement sera permanent et enregistré dans l'historique. Assurez-vous que les informations sont correctes avant de valider.</p>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
                Annuler
            </a>
            <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-6 py-2 bg-red-600 border border-red-600 rounded-md font-medium text-sm text-white hover:bg-red-700 transition-colors disabled:opacity-50">
                <span wire:loading.remove wire:target="submit">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </span>
                <span wire:loading wire:target="submit">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="submit">Enregistrer l'ajustement</span>
                <span wire:loading wire:target="submit">Traitement...</span>
            </button>
        </div>
    </form>
</div>
