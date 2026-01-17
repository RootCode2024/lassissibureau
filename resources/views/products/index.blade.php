<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">Produits</h1>
            
            @can('create', App\Models\Product::class)
                <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-colors shadow-sm">
                    <i data-lucide="plus" class="w-3.5 h-3.5 sm:w-4 sm:h-4"></i>
                    <span class="hidden sm:inline">Nouveau produit</span>
                    <span class="sm:hidden">Nouveau</span>
                </a>
            @endcan
        </div>
    </x-slot>

    {{-- Alerts --}}
    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    {{-- Composant Livewire --}}
    <livewire:products-table />
</x-app-layout>