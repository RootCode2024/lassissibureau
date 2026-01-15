<?php

use function Livewire\Volt\{state};

state(['isMobileMenuOpen' => false]);

?>

<div>
    <!-- Mobile Header -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="h-8 w-auto object-contain">
            <span class="font-bold text-gray-900">{{ config('app.name') }}</span>
        </a>

        <button @click="$wire.isMobileMenuOpen = !$wire.isMobileMenuOpen" class="p-2 rounded-lg hover:bg-gray-100">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0" :class="{ '-translate-x-full': !$wire.isMobileMenuOpen }">

        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-200">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="w-8 h-8 object-contain">
                <span class="font-bold text-gray-900 text-lg">{{ config('app.name') }}</span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto h-[calc(100vh-8rem)]">

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" @class([
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                'bg-red-50 text-red-700' => request()->routeIs('dashboard'),
                'text-gray-700 hover:bg-gray-50' => !request()->routeIs('dashboard'),
            ])>
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Tableau de bord</span>
            </a>

            <!-- Products -->
            @can('products.view')
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Produits</p>

                <a href="{{ route('products.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('products.*') && !request()->routeIs('product-models.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('products.*') || request()->routeIs('product-models.*'),
                ])>
                    <i data-lucide="smartphone" class="w-5 h-5"></i>
                    <span>Produits</span>
                </a>

                @can('products.create')
                <a href="{{ route('product-models.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('product-models.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('product-models.*'),
                ])>
                    <i data-lucide="box" class="w-5 h-5"></i>
                    <span>Modèles</span>
                </a>
                @endcan
            </div>
            @endcan

            <!-- Sales -->
            @can('sales.view')
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ventes</p>

                <a href="{{ route('sales.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('sales.index'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('sales.index'),
                ])>
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    <span>Toutes les ventes</span>
                </a>

                @can('sales.create')
                <a href="{{ route('sales.create') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('sales.create'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('sales.create'),
                ])>
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    <span>Nouvelle vente</span>
                </a>
                @endcan

                @can('resellers.manage')
                <a href="{{ route('sales.resellers') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('sales.resellers'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('sales.resellers'),
                ])>
                    <i data-lucide="store" class="w-5 h-5"></i>
                    <span>Ventes revendeurs</span>
                    @php
                        $pendingCount = \App\Models\Sale::pending()->count();
                    @endphp
                    @if($pendingCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('sales.payments.pending') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('sales.payments.pending'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('sales.payments.pending'),
                ])>
                    <i data-lucide="credit-card" class="w-5 h-5"></i>
                    <span>Paiements en attente</span>
                    @php
                        $unpaidCount = \App\Models\Sale::withPendingPayment()->count();
                    @endphp
                    @if($unpaidCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white bg-amber-600 rounded-full">
                            {{ $unpaidCount }}
                        </span>
                    @endif
                </a>
                @endcan

                @can('returns.manage')
                <a href="{{ route('returns.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('returns.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('returns.*'),
                ])>
                    <i data-lucide="arrow-left-circle" class="w-5 h-5"></i>
                    <span>Retours clients</span>
                </a>
                @endcan
            </div>
            @endcan

            <!-- Stock -->
            @can('stock.view')
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Stock</p>

                <a href="{{ route('stock-movements.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('stock-movements.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('stock-movements.*'),
                ])>
                    <i data-lucide="package" class="w-5 h-5"></i>
                    <span>Mouvements</span>
                </a>
            </div>
            @endcan

            <!-- Resellers -->
            @can('resellers.manage')
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Revendeurs</p>

                <a href="{{ route('resellers.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('resellers.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('resellers.*'),
                ])>
                    <i data-lucide="users" class="w-5 h-5"></i>
                    <span>Revendeurs</span>
                </a>
            </div>
            @endcan

            <!-- Reports -->
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rapports</p>

                <a href="{{ route('reports.daily') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('reports.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('reports.*'),
                ])>
                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                    <span>Statistiques</span>
                </a>
            </div>

            <!-- Administration -->
            @can('users.view')
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Administration</p>

                <a href="{{ route('users.index') }}" @class([
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-red-50 text-red-700' => request()->routeIs('users.*'),
                    'text-gray-700 hover:bg-gray-50' => !request()->routeIs('users.*'),
                ])>
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                    <span>Utilisateurs</span>
                </a>
            </div>
            @endcan

        </nav>

        <!-- User Profile -->
        <div class="border-t border-gray-200 p-4">
            <a href="{{ route('profile') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-red-700 font-medium text-sm">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->primary_role }}</p>
                </div>
            </a>
        </div>
    </aside>

    <!-- Mobile Overlay -->
    <div x-show="$wire.isMobileMenuOpen" @click="$wire.isMobileMenuOpen = false" class="fixed inset-0 bg-gray-900/50 z-30 lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
</div>
