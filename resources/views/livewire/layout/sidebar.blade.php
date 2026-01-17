<?php

use function Livewire\Volt\{state};

?>

<div>
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800 transform transition-transform duration-300 ease-in-out lg:translate-x-0 shadow-2xl" :class="{ '-translate-x-full': !sidebarOpen }">

        <!-- Logo Section -->
        <div class="h-16 flex items-center px-6 border-b border-gray-700/50 bg-gray-900/50 backdrop-blur-sm">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg group-hover:shadow-red-500/50 transition-all duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-5 h-5 object-contain brightness-0 invert">
                </div>
                <span class="font-bold text-white text-lg tracking-tight">{{ config('app.name') }}</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-6 space-y-6 overflow-y-auto h-[calc(100vh-8rem)] scrollbar-thin scrollbar-thumb-gray-700 scrollbar-track-transparent">

            <!-- Dashboard -->
            <div>
                <a href="{{ route('dashboard') }}" @class([
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                    'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('dashboard'),
                    'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('dashboard'),
                ])>
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Tableau de bord</span>
                </a>
            </div>

            <!-- Products Section -->
            @can('products.view')
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Produits</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <div class="space-y-1">
                    <a href="{{ route('products.index') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('products.*') && !request()->routeIs('product-models.*'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('products.*') || request()->routeIs('product-models.*'),
                    ])>
                        <i data-lucide="smartphone" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Produits</span>
                    </a>

                    @can('products.create')
                    <a href="{{ route('product-models.index') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('product-models.*'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('product-models.*'),
                    ])>
                        <i data-lucide="box" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Modèles</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            <!-- Sales Section -->
            @can('sales.view')
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Ventes</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <div class="space-y-1">
                    <a href="{{ route('sales.index') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('sales.index'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('sales.index'),
                    ])>
                        <i data-lucide="shopping-cart" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Toutes les ventes</span>
                    </a>

                    @can('sales.create')
                    <a href="{{ route('sales.create') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('sales.create'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('sales.create'),
                    ])>
                        <i data-lucide="plus-circle" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Nouvelle vente</span>
                    </a>
                    @endcan

                    @can('resellers.manage')
                    <a href="{{ route('sales.resellers') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('sales.resellers'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('sales.resellers'),
                    ])>
                        <i data-lucide="store" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Ventes revendeurs</span>
                        @php
                            $pendingCount = \App\Models\Sale::pending()->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse shadow-lg">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('sales.payments.pending') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('sales.payments.pending'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('sales.payments.pending'),
                    ])>
                        <i data-lucide="credit-card" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Paiements</span>
                        @php
                            $unpaidCount = \App\Models\Sale::withPendingPayment()->count();
                        @endphp
                        @if($unpaidCount > 0)
                            <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-amber-500 rounded-full shadow-lg">
                                {{ $unpaidCount }}
                            </span>
                        @endif
                    </a>
                    @endcan

                    @can('returns.manage')
                    <a href="{{ route('returns.index') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('returns.*'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('returns.*'),
                    ])>
                        <i data-lucide="arrow-left-circle" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Retours clients</span>
                    </a>
                    @endcan
                </div>
            </div>
            @endcan

            <!-- Stock Section -->
            @can('stock.view')
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Stock</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <div class="space-y-1">
                    <a href="{{ route('stock-movements.index') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('stock-movements.*'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('stock-movements.*'),
                    ])>
                        <i data-lucide="package" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Mouvements</span>
                    </a>

                    <a href="{{ route('products.supplier-returns') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('products.supplier-returns'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('products.supplier-returns'),
                    ])>
                        <i data-lucide="truck" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Retours fournisseurs</span>
                    </a>
                </div>
            </div>
            @endcan

            <!-- Trade-ins Section -->
            @can('viewAny', \App\Models\TradeIn::class)
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Trocs</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <a href="{{ route('trade-ins.index') }}" @class([
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                    'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('trade-ins.index'),
                    'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('trade-ins.index'),
                ])>
                    <i data-lucide="repeat" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Historique</span>
                </a>
            </div>
            @endcan

            <!-- Resellers Section -->
            @can('resellers.manage')
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Revendeurs</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <a href="{{ route('resellers.index') }}" @class([
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                    'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('resellers.*'),
                    'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('resellers.*'),
                ])>
                    <i data-lucide="users" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Revendeurs</span>
                </a>
            </div>
            @endcan

            <!-- Reports Section -->
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Rapports</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                @if(auth()->user()->isVendeur())
                    <a href="{{ route('reports.daily') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('reports.daily'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('reports.daily'),
                    ])>
                        <i data-lucide="calendar" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Mes ventes</span>
                    </a>
                @endif

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('reports.daily') }}" @class([
                        'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                        'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('reports.*'),
                        'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('reports.*'),
                    ])>
                        <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                        <span>Statistiques</span>
                    </a>
                @endif
            </div>

            <!-- Administration Section -->
            @can('users.view')
            <div>
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <span class="w-4 h-px bg-gray-700"></span>
                    <span>Administration</span>
                    <span class="flex-1 h-px bg-gray-700"></span>
                </p>

                <a href="{{ route('users.index') }}" @class([
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                    'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('users.*'),
                    'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('users.*'),
                ])>
                    <i data-lucide="user-cog" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Utilisateurs</span>
                </a>

                <a href="{{ route('activity-logs.index') }}" @class([
                    'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-200 group',
                    'bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg shadow-red-500/30' => request()->routeIs('activity-logs.index'),
                    'text-gray-300 hover:bg-gray-800/50 hover:text-white' => !request()->routeIs('activity-logs.index'),
                ])>
                    <i data-lucide="activity" class="w-5 h-5 flex-shrink-0"></i>
                    <span>Activités</span>
                </a>
            </div>
            @endcan

        </nav>

        <!-- User Profile -->
        <div class="border-t border-gray-700/50 p-4 bg-gray-900/50 backdrop-blur-sm">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-gray-800/50 transition-all duration-200 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center flex-shrink-0 shadow-lg group-hover:shadow-red-500/50 transition-all duration-300">
                    <span class="text-white font-semibold text-base">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->primary_role }}</p>
                </div>
                <i data-lucide="chevron-right" class="w-4 h-4 text-gray-500 group-hover:text-gray-300 transition-colors"></i>
            </a>
        </div>
    </aside>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false" 
         class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-40 lg:hidden" 
         x-transition:enter="transition-opacity ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition-opacity ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0">
    </div>
</div>