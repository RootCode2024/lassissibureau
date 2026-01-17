<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Produits</h1>
                <p class="text-sm text-gray-500 mt-1">Gérez votre inventaire de produits</p>
            </div>
        </div>
    </x-slot>

    {{-- Bouton d'action dans le slot "actions" --}}
    <x-slot name="actions">
        <a href="{{ route('products.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-black to-gray-900 text-white rounded-lg text-sm font-medium hover:from-gray-900 hover:to-gray-800 transition-all duration-200 shadow-sm hover:shadow-md whitespace-nowrap">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span class="hidden sm:inline">Nouveau produit</span>
            <span class="sm:hidden">Nouveau</span>
        </a>
    </x-slot>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    <livewire:products-table />
</x-app-layout>