<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900">Mouvements de stock</h2>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\StockMovement::class)
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('stock-movements.create.reception') }}" class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-green-600 border border-green-600 rounded-lg font-medium text-xs sm:text-sm text-white hover:bg-green-700 active:bg-green-800 transition-all hover:shadow-lg">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                    <span>Réception</span>
                </a>
                <a href="{{ route('stock-movements.create.adjustment') }}" class="inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-red-600 border border-red-600 rounded-lg font-medium text-xs sm:text-sm text-white hover:bg-red-700 active:bg-red-800 transition-all hover:shadow-lg">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    <span>Ajustement</span>
                </a>
            </div>
        @endcan
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 sm:gap-4">
                <div>
                    <label for="type" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Type de mouvement
                    </label>
                    <select name="type" id="type" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                        <option value="">Tous les types</option>
                        @foreach(App\Enums\StockMovementType::cases() as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Date début
                    </label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                </div>

                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-1.5 sm:mb-2">
                        Date fin
                    </label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full py-2 rounded-lg border-gray-300 shadow-sm focus:border-gray-900 focus:ring-1 focus:ring-gray-900 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-3 sm:px-4 py-2 bg-gray-900 border border-gray-900 rounded-lg font-medium text-sm text-white hover:bg-gray-800 active:bg-gray-950 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        <span>Filtrer</span>
                    </button>
                    @if(request()->hasAny(['type', 'date_from', 'date_to']))
                        <a href="{{ route('stock-movements.index') }}" class="px-3 sm:px-4 py-2 bg-white border border-gray-300 rounded-lg font-medium text-sm text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors" title="Réinitialiser">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Vue Desktop (Tableau) --}}
        @if($movements->count() > 0)
            <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Date
                                </th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Type
                                </th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Produit
                                </th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    État → Localisation
                                </th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Utilisateur
                                </th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($movements as $movement)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $movement->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-{{ $movement->type->color() }}-100 flex items-center justify-center flex-shrink-0">
                                                <i data-lucide="{{ $movement->type->icon() }}" class="w-4 h-4 text-{{ $movement->type->color() }}-600"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-gray-900 truncate">{{ $movement->type->label() }}</div>
                                                @if($movement->isIncrement())
                                                    <div class="text-xs font-semibold text-green-600">+{{ $movement->quantity }}</div>
                                                @else
                                                    <div class="text-xs font-semibold text-red-600">-{{ $movement->quantity }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $movement->product->productModel->name }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ $movement->product->productModel->brand }}</div>
                                            @if($movement->product->imei)
                                                <div class="text-xs text-gray-400 font-mono truncate">{{ $movement->product->imei }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4">
                                        <div class="flex flex-col gap-1">
                                            @php
                                                $stateBefore = $movement->state_before ? App\Enums\ProductState::tryFrom($movement->state_before) : null;
                                                $locBefore = $movement->location_before ? App\Enums\ProductLocation::tryFrom($movement->location_before) : null;
                                                
                                                $stateAfter = $movement->state_after ? App\Enums\ProductState::tryFrom($movement->state_after) : null;
                                                $locAfter = $movement->location_after ? App\Enums\ProductLocation::tryFrom($movement->location_after) : null;
                                            @endphp

                                            @if($stateBefore || $locBefore)
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <span class="{{ $stateBefore?->badgeClasses() ?? '' }}">{{ $stateBefore?->label() ?? '-' }}</span>
                                                    <span class="text-gray-300">/</span>
                                                    <span>{{ $locBefore?->label() ?? '-' }}</span>
                                                </div>
                                                <div class="text-xs text-gray-400 pl-2">
                                                    <i data-lucide="arrow-down" class="w-3 h-3"></i>
                                                </div>
                                            @endif

                                            <div class="text-sm font-medium text-gray-900 flex items-center gap-1">
                                                <span class="{{ $stateAfter?->badgeClasses() ?? '' }}">{{ $stateAfter?->label() ?? '-' }}</span>
                                                <span class="text-gray-300">/</span>
                                                <span class="{{ $locAfter?->badgeClasses() ?? '' }}">{{ $locAfter?->label() ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 truncate">{{ $movement->user->name }}</div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-700 hover:text-gray-900 border border-gray-200 hover:border-gray-300 hover:bg-gray-50 rounded-lg transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span class="hidden xl:inline">Voir</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 xl:px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $movements->links() }}
                </div>
            </div>

            {{-- Vue Mobile/Tablet (Cards) --}}
            <div class="lg:hidden space-y-3">
                @foreach($movements as $movement)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-all">
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-gray-100">
                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-{{ $movement->type->color() }}-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $movement->type->icon() }}" class="w-4 h-4 text-{{ $movement->type->color() }}-600"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $movement->type->label() }}</p>
                                    <p class="text-xs text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @if($movement->isIncrement())
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold text-green-700 bg-green-100 border border-green-200 flex-shrink-0">
                                    +{{ $movement->quantity }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold text-red-700 bg-red-100 border border-red-200 flex-shrink-0">
                                    -{{ $movement->quantity }}
                                </span>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="space-y-2.5 mb-3">
                            <div class="flex items-start gap-2">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500">Produit</p>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $movement->product->productModel->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $movement->product->productModel->brand }}</p>
                                    @if($movement->product->imei)
                                        <p class="text-xs text-gray-400 font-mono truncate">{{ $movement->product->imei }}</p>
                                    @endif
                                </div>
                            </div>

                            @php
                                $stateBefore = $movement->state_before ? App\Enums\ProductState::tryFrom($movement->state_before) : null;
                                $locBefore = $movement->location_before ? App\Enums\ProductLocation::tryFrom($movement->location_before) : null;
                                
                                $stateAfter = $movement->state_after ? App\Enums\ProductState::tryFrom($movement->state_after) : null;
                                $locAfter = $movement->location_after ? App\Enums\ProductLocation::tryFrom($movement->location_after) : null;
                            @endphp

                            @if($stateBefore || $locBefore || $stateAfter || $locAfter)
                                <div class="flex items-start gap-2">
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500 mb-1">Changement</p>
                                        <div class="space-y-1">
                                            @if($stateBefore || $locBefore)
                                                <div class="text-xs text-gray-500 flex items-center gap-1">
                                                    <span class="{{ $stateBefore?->badgeClasses() ?? '' }}">{{ $stateBefore?->label() ?? '-' }}</span>
                                                    <span>/</span>
                                                    <span>{{ $locBefore?->label() ?? '-' }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center gap-1">
                                                <i data-lucide="arrow-down" class="w-3 h-3 text-gray-400"></i>
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 flex items-center gap-1">
                                                <span class="{{ $stateAfter?->badgeClasses() ?? '' }}">{{ $stateAfter?->label() ?? '-' }}</span>
                                                <span>/</span>
                                                <span class="{{ $locAfter?->badgeClasses() ?? '' }}">{{ $locAfter?->label() ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-start gap-2">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500">Utilisateur</p>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $movement->user->name }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Action --}}
                        <a href="{{ route('stock-movements.show', $movement) }}" class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 border border-gray-200 rounded-lg hover:bg-gray-50 active:bg-gray-100 transition-colors w-full">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>Voir les détails</span>
                        </a>
                    </div>
                @endforeach

                <div class="px-3 py-2.5 bg-white border border-gray-200 rounded-lg">
                    {{ $movements->links() }}
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl p-8 sm:p-12 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-16 sm:h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i data-lucide="package-x" class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Aucun mouvement de stock</p>
                <p class="text-xs text-gray-500 mt-1">Les mouvements apparaîtront ici</p>
            </div>
        @endif
    </div>
</x-app-layout>