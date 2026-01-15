<x-app-layout>
    <x-slot name="header">
        Revendeurs
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('resellers.create') }}"
           class="inline-flex items-center gap-2 px-3 py-2 text-sm rounded-lg bg-gray-900 text-white hover:bg-gray-800">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Nouveau
        </a>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-8 bg-gray-50 p-6 rounded-xl">

        {{-- FILTRES --}}
        <div class="bg-white border border-gray-200 rounded-xl p-5">
            <form method="GET" action="{{ route('resellers.index') }}"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-gray-500 mb-1 block">
                        Recherche
                    </label>
                    <input
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nom ou téléphone"
                        class="w-full rounded-md border-gray-300 text-sm focus:ring-gray-900 focus:border-gray-900"
                    />
                </div>

                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 mb-1 block">
                        Statut
                    </label>
                    <select
                        name="is_active"
                        class="w-full rounded-md border-gray-300 text-sm focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Tous</option>
                        <option value="1" @selected(request('is_active') === '1')>Actifs</option>
                        <option value="0" @selected(request('is_active') === '0')>Inactifs</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-sm hover:bg-gray-800">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Filtrer
                    </button>

                    @if(request()->hasAny(['search', 'is_active']))
                        <a href="{{ route('resellers.index') }}"
                           class="px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- LISTE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($resellers as $reseller)
                <div class="bg-white border border-gray-200 rounded-xl p-5 hover:shadow-sm transition">
                    <div class="flex items-start justify-between mb-4">
                        <div class="space-y-1">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $reseller->name }}
                            </p>

                            @if($reseller->is_active)
                                <span class="inline-flex px-2 py-0.5 text-xs rounded-md bg-green-100 text-green-700">
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex px-2 py-0.5 text-xs rounded-md bg-gray-100 text-gray-600">
                                    Inactif
                                </span>
                            @endif
                        </div>

                        <i data-lucide="store" class="w-5 h-5 text-gray-400"></i>
                    </div>

                    <div class="text-sm text-gray-600 space-y-1 mb-4">
                        <div class="flex items-center gap-1">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            {{ $reseller->phone }}
                        </div>

                        @if($reseller->address)
                            <div class="flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                <span class="truncate">{{ $reseller->address }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- STATS --}}
                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Ventes</p>
                            <p class="font-semibold text-gray-900">{{ $reseller->confirmed_sales_count }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">En cours</p>
                            <p class="font-semibold text-gray-900">{{ $reseller->pending_sales_count }}</p>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <a href="{{ route('resellers.show', $reseller) }}"
                           class="flex-1 inline-flex justify-center px-3 py-2 text-sm border rounded-lg hover:bg-gray-50">
                            Voir
                        </a>
                        <a href="{{ route('resellers.statistics', $reseller) }}"
                           class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                        </a>
                        <a href="{{ route('resellers.edit', $reseller) }}"
                           class="px-3 py-2 border rounded-lg hover:bg-gray-50">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-xl p-10 text-center">
                    <i data-lucide="users-x" class="w-10 h-10 text-gray-400 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-500 mb-4">
                        Aucun revendeur trouvé
                    </p>
                    <a href="{{ route('resellers.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-sm rounded-lg hover:bg-gray-800">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Ajouter
                    </a>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($resellers->hasPages())
            <div class="bg-white border border-gray-200 rounded-xl p-4">
                {{ $resellers->links() }}
            </div>
        @endif

    </div>
</x-app-layout>
