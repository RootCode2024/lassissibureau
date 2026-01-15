<x-app-layout>
    <x-slot name="header">
        Détails du mouvement
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        {{-- En-tête du mouvement --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="bg-{{ $stockMovement->type->color() }}-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i data-lucide="{{ $stockMovement->type->icon() }}" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $stockMovement->type->label() }}</h3>
                            <p class="text-sm text-white/80">{{ $stockMovement->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                    @if($stockMovement->isIncrement())
                        <div class="text-2xl font-bold text-white">+{{ $stockMovement->quantity }}</div>
                    @else
                        <div class="text-2xl font-bold text-white">-{{ $stockMovement->quantity }}</div>
                    @endif
                </div>
            </div>

            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            État avant
                        </dt>
                        <dd class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ App\Enums\ProductState::from($stockMovement->state_before)->badgeClasses() }}">
                            {{ App\Enums\ProductState::from($stockMovement->state_before)->label() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            État après
                        </dt>
                        <dd class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ App\Enums\ProductState::from($stockMovement->state_after)->badgeClasses() }}">
                            {{ App\Enums\ProductState::from($stockMovement->state_after)->label() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Localisation avant
                        </dt>
                        <dd class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ App\Enums\ProductLocation::from($stockMovement->location_before)->badgeClasses() }}">
                            <i data-lucide="{{ App\Enums\ProductLocation::from($stockMovement->location_before)->icon() }}" class="w-3 h-3 mr-1.5"></i>
                            {{ App\Enums\ProductLocation::from($stockMovement->location_before)->label() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Localisation après
                        </dt>
                        <dd class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ App\Enums\ProductLocation::from($stockMovement->location_after)->badgeClasses() }}">
                            <i data-lucide="{{ App\Enums\ProductLocation::from($stockMovement->location_after)->icon() }}" class="w-3 h-3 mr-1.5"></i>
                            {{ App\Enums\ProductLocation::from($stockMovement->location_after)->label() }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Effectué par
                        </dt>
                        <dd class="text-sm text-gray-900 font-medium">{{ $stockMovement->user->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Date et heure
                        </dt>
                        <dd class="text-sm text-gray-900">{{ $stockMovement->created_at->format('d/m/Y à H:i:s') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        {{-- Produit concerné --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                    <i data-lucide="package" class="w-5 h-5 text-white"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Produit concerné</h3>
            </div>

            <div class="flex items-start gap-4">
                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    @php
                        $categoryIcons = ['telephone' => 'smartphone', 'tablette' => 'tablet', 'pc' => 'monitor', 'accessoire' => 'box'];
                        $icon = $categoryIcons[$stockMovement->product->productModel->category] ?? 'box';
                    @endphp
                    <i data-lucide="{{ $icon }}" class="w-8 h-8 text-gray-600"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">{{ $stockMovement->product->productModel->name }}</h4>
                            <p class="text-sm text-gray-500 mt-1">{{ $stockMovement->product->productModel->brand }}</p>
                            @if($stockMovement->product->imei)
                                <p class="text-xs font-mono text-gray-500 mt-2 bg-gray-50 inline-block px-2 py-1 rounded">
                                    IMEI: {{ $stockMovement->product->imei }}
                                </p>
                            @endif
                        </div>
                        <a href="{{ route('products.show', $stockMovement->product) }}" class="text-sm text-gray-600 hover:text-gray-900 transition-colors">
                            Voir le produit →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Justification (si présente) --}}
        @if($stockMovement->justification)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="file-warning" class="w-5 h-5 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-amber-900 uppercase tracking-wide mb-2">Justification</h3>
                        <p class="text-sm text-amber-800">{{ $stockMovement->justification }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Notes --}}
        @if($stockMovement->notes)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Notes</h3>
                </div>
                <p class="text-sm text-gray-700">{{ $stockMovement->notes }}</p>
            </div>
        @endif

        {{-- Vente associée (si applicable) --}}
        @if($stockMovement->sale)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Vente associée</h3>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-700">Vente #{{ $stockMovement->sale->id }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockMovement->sale->date_vente_effective->format('d/m/Y') }}</p>
                    </div>
                    <a href="{{ route('sales.show', $stockMovement->sale) }}" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                        Voir la vente →
                    </a>
                </div>
            </div>
        @endif

        {{-- Revendeur (si applicable) --}}
        @if($stockMovement->reseller)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-white"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Revendeur</h3>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $stockMovement->reseller->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stockMovement->reseller->phone }}</p>
                    </div>
                    <a href="{{ route('resellers.show', $stockMovement->reseller) }}" class="text-sm text-amber-600 hover:text-amber-800 transition-colors">
                        Voir le revendeur →
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
