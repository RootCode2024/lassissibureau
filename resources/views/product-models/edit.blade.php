<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900 truncate">Modifier le modèle</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5 sm:mt-1 truncate">{{ $productModel->name }}</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <a
            href="{{ route('product-models.index') }}"
            class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-xs sm:text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors w-full sm:w-auto"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Retour</span>
        </a>
    </x-slot>

    <x-alerts.success :message="session('success')" />

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm">
        <form method="POST" action="{{ route('product-models.update', $productModel) }}" class="p-4 sm:p-6 lg:p-8">
            @csrf
            @method('PUT')
            @include('product-models._form')
        </form>
    </div>
</x-app-layout>