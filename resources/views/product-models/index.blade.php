<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900 truncate">Modèles de produits</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1">Gérez vos modèles de produits</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\ProductModel::class)
            <a 
                href="{{ route('product-models.create') }}" 
                class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-black border border-black rounded-md font-medium text-xs sm:text-sm text-white hover:bg-gray-800 active:bg-gray-900 transition-colors w-full sm:w-auto"
            >
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="hidden xs:inline">Nouveau modèle</span>
                <span class="xs:hidden">Nouveau</span>
            </a>
        @endcan
    </x-slot>

    <livewire:product-models-table />
</x-app-layout>