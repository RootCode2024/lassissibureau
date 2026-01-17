<?php

use function Livewire\Volt\{state};

state(['searchQuery' => '', 'mobileSearchOpen' => false]);

$search = function() {
    if (strlen($this->searchQuery) >= 15) {
        // C'est probablement un IMEI
        return redirect()->route('products.search.imei', ['imei' => $this->searchQuery]);
    }
    // Sinon, rediriger vers la recherche de produits
    return redirect()->route('products.index', ['search' => $this->searchQuery]);
};

$toggleMobileSearch = function() {
    $this->mobileSearchOpen = !$this->mobileSearchOpen;
};

?>

<div class="sticky top-0 z-30 bg-gray-900 border-b border-gray-700/50 shadow-xl">
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Top Bar (Desktop & Mobile) -->
        <div class="flex items-center justify-between h-14 sm:h-16 gap-2 sm:gap-4">
            <!-- Left Section: Mobile Menu & Logo -->
            <div class="flex items-center gap-2">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 -ml-2 text-gray-400 rounded-lg hover:bg-gray-800/50 lg:hidden transition-colors">
                    <i data-lucide="menu" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                </button>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 md:hidden">
                     <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-7 sm:h-8 w-auto object-contain brightness-0 invert">
                </a>
            </div>

            <!-- Search Bar (Desktop Only) -->
            <div class="hidden lg:flex flex-1 max-w-lg">
                <form wire:submit="search" class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-5 h-5 text-gray-500"></i>
                    </div>
                    <input
                        type="text"
                        wire:model="searchQuery"
                        placeholder="Rechercher par IMEI ou nom de produit..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-700 bg-gray-800 text-gray-200 rounded-lg leading-5 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm transition-all"
                    >
                </form>
            </div>

            <!-- Right Side Actions -->
            <div class="flex items-center gap-1 sm:gap-2 lg:gap-4">
                <!-- Search Icon (Mobile Only) -->
                <button
                    wire:click="toggleMobileSearch"
                    class="lg:hidden p-2 text-gray-400 rounded-lg hover:bg-gray-800/50 transition-colors"
                    title="Rechercher"
                >
                    <i data-lucide="search" class="w-5 h-5"></i>
                </button>

                <!-- Quick Actions - New Sale -->
                @can('sales.create')
                <a
                    href="{{ route('sales.create') }}"
                    class="inline-flex items-center gap-2 px-3 py-2 sm:px-4 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-medium rounded-lg transition-all shadow-lg shadow-red-500/30 hover:shadow-red-500/40"
                    title="Nouvelle vente"
                >
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Nouvelle vente</span>
                </a>
                @endcan

                <!-- Trade-ins Pending (Admin Only) -->
                @if(auth()->user()->hasRole('admin'))
                    <a 
                        href="{{ route('trade-ins.pending') }}" 
                        class="relative flex items-center gap-2 px-2 sm:px-3 py-2 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-800/50 hover:text-white transition-colors"
                        title="Trocs en attente"
                    >
                        <i data-lucide="repeat" class="w-4 h-4"></i>
                        <span class="hidden xl:inline">Trocs en attente</span>
                        @php
                            $pendingCount = \App\Models\TradeIn::pending()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="absolute -top-1 -right-1 xl:static xl:inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-xs font-bold bg-purple-500 text-white shadow-lg animate-pulse">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif

                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button
                        @click="open = !open"
                        class="flex items-center gap-1 sm:gap-2 p-1.5 sm:p-2 rounded-lg hover:bg-gray-800/50 transition-colors"
                        title="Menu utilisateur"
                    >
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg">
                            <span class="text-white font-semibold text-xs sm:text-sm">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </span>
                        </div>
                        <i data-lucide="chevron-down" class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 hidden sm:block"></i>
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
                        class="absolute right-0 mt-2 w-48 rounded-lg shadow-xl bg-gray-800 border border-gray-700 ring-1 ring-black ring-opacity-5"
                        style="display: none;"
                    >
                        <div class="py-1">
                            <div class="px-4 py-2 text-xs text-gray-400 border-b border-gray-700">
                                {{ auth()->user()->name }}
                            </div>
                            <a href="{{ route('profile') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-gray-700/50 hover:text-white transition-colors">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                <span>Mon profil</span>
                            </a>
                            <hr class="my-1 border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    <span>Déconnexion</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Search Bar (Expandable) -->
        <div 
            x-show="$wire.mobileSearchOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden pb-3 pt-1"
            style="display: none;"
        >
            <form wire:submit="search" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i data-lucide="search" class="w-5 h-5 text-gray-500"></i>
                </div>
                <input
                    type="text"
                    wire:model="searchQuery"
                    placeholder="Rechercher par IMEI ou nom..."
                    class="block w-full pl-10 pr-10 py-2.5 border border-gray-700 bg-gray-800 text-gray-200 rounded-lg leading-5 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent text-sm transition-all"
                    autofocus
                >
                <button 
                    type="button"
                    wire:click="toggleMobileSearch"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                >
                    <i data-lucide="x" class="w-5 h-5 text-gray-500 hover:text-gray-300 transition-colors"></i>
                </button>
            </form>
        </div>
    </div>
</div>