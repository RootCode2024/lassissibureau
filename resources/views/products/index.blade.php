<x-app-layout>
    <x-slot name="header">
        Produits
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\Product::class)
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition-colors shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouveau produit
            </a>
        @endcan
    </x-slot>

    {{-- Alerts --}}
    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    {{-- Composant Livewire --}}
    <livewire:products-table />
</x-app-layout>
