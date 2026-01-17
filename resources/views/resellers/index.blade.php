<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900">Revendeurs</h2>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('resellers.create') }}"
           class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 text-xs sm:text-sm rounded-lg bg-gray-900 text-white hover:bg-gray-800 active:bg-gray-950 transition-colors w-full sm:w-auto">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span class="hidden xs:inline">Nouveau revendeur</span>
            <span class="xs:hidden">Nouveau</span>
        </a>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-4 sm:space-y-6 lg:space-y-8">

        {{-- FILTRES --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm">
            <form method="GET" action="{{ route('resellers.index') }}"
                  class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">

                <div class="md:col-span-2">
                    <label class="text-xs uppercase tracking-wide text-gray-500 mb-1.5 sm:mb-2 block font-medium">
                        Recherche
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Nom ou téléphone"
                            class="w-full pl-10 rounded-lg border-gray-300 text-sm focus:ring-1 focus:ring-gray-900 focus:border-gray-900"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-xs uppercase tracking-wide text-gray-500 mb-1.5 sm:mb-2 block font-medium">
                        Statut
                    </label>
                    <select
                        name="is_active"
                        class="w-full rounded-lg border-gray-300 text-sm focus:ring-1 focus:ring-gray-900 focus:border-gray-900">
                        <option value="">Tous</option>
                        <option value="1" @selected(request('is_active') === '1')>Actifs</option>
                        <option value="0" @selected(request('is_active') === '0')>Inactifs</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 active:bg-gray-950 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Filtrer</span>
                    </button>

                    @if(request()->hasAny(['search', 'is_active']))
                        <a href="{{ route('resellers.index') }}"
                           class="px-3 py-2 rounded-lg border border-gray-300 bg-white hover:bg-gray-50 active:bg-gray-100 transition-colors"
                           title="Réinitialiser">
                            <i data-lucide="x" class="w-4 h-4 text-gray-600"></i>
                        </a>
                    @endif
                </div>

            </form>
        </div>

        {{-- LISTE --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 lg:gap-6">
            @forelse($resellers as $reseller)
                <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 hover:shadow-md hover:border-gray-300 transition-all">
                    <div class="flex items-start justify-between gap-3 mb-3 sm:mb-4">
                        <div class="space-y-1 min-w-0 flex-1">
                            <p class="text-sm sm:text-base font-semibold text-gray-900 truncate">
                                {{ $reseller->name }}
                            </p>

                            @if($reseller->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-md bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Actif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-md bg-gray-100 text-gray-600 border border-gray-200">
                                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                                    Inactif
                                </span>
                            @endif
                        </div>

                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i data-lucide="store" class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600"></i>
                        </div>
                    </div>

                    <div class="text-xs sm:text-sm text-gray-600 space-y-1.5 mb-3 sm:mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="phone" class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0"></i>
                            <span class="truncate">{{ $reseller->phone }}</span>
                        </div>

                        @if($reseller->address)
                            <div class="flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0"></i>
                                <span class="truncate">{{ $reseller->address }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- STATS --}}
                    <div class="grid grid-cols-2 gap-3 sm:gap-4 text-sm mb-3 sm:mb-4 pb-3 sm:pb-4 border-b border-gray-100">
                        <div class="text-center sm:text-left">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">Ventes</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">{{ $reseller->confirmed_sales_count }}</p>
                        </div>
                        <div class="text-center sm:text-left">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-0.5">En cours</p>
                            <p class="text-base sm:text-lg font-bold text-gray-900">{{ $reseller->pending_sales_count }}</p>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <a href="{{ route('resellers.show', $reseller) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-medium border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 active:bg-gray-100 transition-colors">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>Voir</span>
                        </a>
                        <a href="{{ route('resellers.statistics', $reseller) }}"
                           class="inline-flex items-center justify-center px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 active:bg-gray-100 transition-colors"
                           title="Statistiques">
                            <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-600"></i>
                        </a>
                        <a href="{{ route('resellers.edit', $reseller) }}"
                           class="inline-flex items-center justify-center px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 active:bg-gray-100 transition-colors"
                           title="Modifier">
                            <i data-lucide="edit" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-600"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-xl p-8 sm:p-12 text-center shadow-sm">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <i data-lucide="users-x" class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-900 mb-1">Aucun revendeur trouvé</p>
                    <p class="text-xs sm:text-sm text-gray-500 mb-3 sm:mb-4">
                        Commencez par ajouter votre premier revendeur
                    </p>
                    <a href="{{ route('resellers.create') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-800 active:bg-gray-950 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Ajouter un revendeur
                    </a>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($resellers->hasPages())
            <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 shadow-sm">
                {{ $resellers->links() }}
            </div>
        @endif

    </div>
</x-app-layout>