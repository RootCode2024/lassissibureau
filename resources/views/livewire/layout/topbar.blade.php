<?php

use function Livewire\Volt\{state};

state(['searchQuery' => '']);

$search = function() {
    if (strlen($this->searchQuery) >= 15) {
        // C'est probablement un IMEI
        return redirect()->route('products.search.imei', ['imei' => $this->searchQuery]);
    }
    // Sinon, rediriger vers la recherche de produits
    return redirect()->route('products.index', ['search' => $this->searchQuery]);
};

?>

<div class="sticky top-0 z-30 bg-white border-b border-gray-200">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Search Bar -->
            <div class="flex-1 max-w-lg">
                <form wire:submit="search" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        wire:model="searchQuery"
                        placeholder="Rechercher par IMEI ou nom de produit..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm"
                    >
                </form>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-4 ml-4">
                <!-- Quick Actions -->
                @can('sales.create')
                <a
                    href="{{ route('sales.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Nouvelle vente</span>
                </a>
                @endcan

                <!-- Notifications -->
                <button class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute top-1 right-1 block h-2 w-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                </button>

                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('trade-ins.pending') }}" class="flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                        <i data-lucide="repeat" class="w-4 h-4"></i>
                        <span>Trocs en attente</span>
                        @php
                            $pendingCount = \App\Models\TradeIn::pending()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-purple-600 text-white">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center">
                            <span class="text-red-700 font-medium text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                    </button>

                    <!-- Dropdown -->
                    <div
                        x-show="open"
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                    >
                        <div class="py-1">
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span>Mon profil</span>
                            </a>
                            <hr class="my-1 border-gray-200">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
