<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Produits nécessitant une attention') }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Tous les produits
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Info --}}
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700">
                            Cette page liste les produits à réparer, les retours clients et les produits perdus/volés nécessitant un traitement.
                        </p>
                    </div>
                </div>
            </div>

            @if($products->isEmpty())
                <x-empty-state
                    title="Aucun produit nécessitant attention"
                    description="Tous vos produits sont en bon état ! 🎉"
                />
            @else
                {{-- Grouper par état --}}
                @php
                    $grouped = $products->groupBy('state');
                @endphp

                @foreach($grouped as $state => $stateProducts)
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <x-products.state-badge :state="$state" class="text-base px-4 py-2" />
                            <span class="ml-3 text-sm text-gray-600">({{ $stateProducts->count() }} produits)</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($stateProducts as $product)
                                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $product->productModel->name }}</h3>
                                                <p class="text-sm text-gray-500">{{ $product->productModel->brand }}</p>
                                            </div>
                                        </div>

                                        <div class="space-y-2 text-sm mb-4">
                                            @if($product->imei)
                                                <p class="text-gray-600 font-mono">{{ $product->imei }}</p>
                                            @endif

                                            @if($product->defauts)
                                                <div class="bg-red-50 border border-red-200 rounded p-2">
                                                    <p class="text-xs text-red-800"><strong>Défauts:</strong> {{ Str::limit($product->defauts, 100) }}</p>
                                                </div>
                                            @endif

                                            @if($product->lastMovement)
                                                <p class="text-xs text-gray-500">
                                                    Dernier mouvement: {{ $product->lastMovement->created_at->diffForHumans() }}
                                                </p>
                                            @endif
                                        </div>

                                        <div class="flex gap-2">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="flex-1 text-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md text-sm font-medium transition-colors">
                                                Détails
                                            </a>
                                            @can('update', $product)
                                                <a href="{{ route('products.edit', $product) }}"
                                                   class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors">
                                                    Traiter
                                                </a>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>
