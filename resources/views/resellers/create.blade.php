<x-app-layout>
    <x-slot name="header">
        Nouveau revendeur
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('resellers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        @livewire('create-reseller')
    </div>
</x-app-layout>
