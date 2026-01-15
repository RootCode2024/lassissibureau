<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900">Modèles de produits</h2>
                <p class="text-sm text-gray-500 mt-1">Gérez vos modèles de produits</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\ProductModel::class)
            <a href="{{ route('product-models.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-black border border-black rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Nouveau modèle
            </a>
        @endcan
    </x-slot>

    <livewire:product-models-table />
</x-app-layout>
