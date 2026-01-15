<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-900">Créer un modèle de produit</h2>
                <p class="text-sm text-gray-500 mt-1">Ajoutez un nouveau modèle à votre catalogue</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <a
            href="{{ route('product-models.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <div class="bg-white border border-gray-200 rounded-lg">
        <form method="POST" action="{{ route('product-models.store') }}" class="p-8">
            @csrf
            @include('product-models._form')
        </form>
    </div>
</x-app-layout>
