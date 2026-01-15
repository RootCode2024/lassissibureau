<x-app-layout>
    <x-slot name="header">
        Mouvements de stock
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\StockMovement::class)
            <div class="flex items-center gap-2">
                <a href="{{ route('stock-movements.create.reception') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 border border-green-600 rounded-md font-medium text-sm text-white hover:bg-green-700 transition-colors">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                    Réception
                </a>
                <a href="{{ route('stock-movements.create.adjustment') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 border border-red-600 rounded-md font-medium text-sm text-white hover:bg-red-700 transition-colors">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    Ajustement
                </a>
            </div>
        @endcan
    </x-slot>

    <div class="space-y-6">
        {{-- Filtres --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="type" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Type de mouvement
                    </label>
                    <select name="type" id="type" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Tous les types</option>
                        @foreach(App\Enums\StockMovementType::cases() as $type)
                            <option value="{{ $type->value }}" {{ request('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_from" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Date début
                    </label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                </div>

                <div>
                    <label for="date_to" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Date fin
                    </label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="block w-full py-2 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-900 border border-gray-900 rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Filtrer
                    </button>
                    @if(request()->hasAny(['type', 'date_from', 'date_to']))
                        <a href="{{ route('stock-movements.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Liste des mouvements --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            @if($movements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    État → Localisation
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilisateur
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($movements as $movement)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $movement->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-lg bg-{{ $movement->type->color() }}-100 flex items-center justify-center">
                                                <i data-lucide="{{ $movement->type->icon() }}" class="w-4 h-4 text-{{ $movement->type->color() }}-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $movement->type->label() }}</div>
                                                @if($movement->isIncrement())
                                                    <div class="text-xs text-green-600">+{{ $movement->quantity }}</div>
                                                @else
                                                    <div class="text-xs text-red-600">-{{ $movement->quantity }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $movement->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $movement->product->productModel->brand }}</div>
                                        @if($movement->product->imei)
                                            <div class="text-xs text-gray-400 font-mono">{{ $movement->product->imei }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $movement->user->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('stock-movements.show', $movement) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                                            <i data-lucide="eye" class="w-4 h-4 inline-block"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $movements->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i data-lucide="package-x" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-sm text-gray-500">Aucun mouvement de stock trouvé</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
