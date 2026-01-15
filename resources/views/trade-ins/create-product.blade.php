<x-app-layout>
    <x-slot name="header">
        Créer le produit reçu en troc
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('trade-ins.pending') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        @livewire('create-trade-in-product', ['tradeIn' => $tradeIn])
    </div>
</x-app-layout>
