<div>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-900">Tableau de bord</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Vue d'ensemble de votre activité</p>
            </div>
            <div class="text-xs sm:text-sm text-gray-500">
                {{ now()->locale('fr')->translatedFormat('d F Y') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 sm:space-y-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- Ventes du jour -->
            <div class="group relative bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                            <i data-lucide="trending-up" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Ventes</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ $stats['sales_today'] }}</p>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-0.5 sm:gap-2">
                        <span class="text-xs sm:text-sm font-semibold text-emerald-600 truncate">
                            {{ number_format($stats['revenue_today'], 0, ',', ' ') }}
                        </span>
                        <span class="text-xs text-gray-500">FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Bénéfice du jour (ADMIN ONLY) -->
            @can('viewAny', App\Models\Sale::class)
                @if(auth()->user()->isAdmin())
                    <div class="group relative bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-teal-500/5 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative">
                            <div class="mb-3 sm:mb-4">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                    <i data-lucide="wallet" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                                </div>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Bénéfice</p>
                            <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2 truncate">{{ number_format($stats['profit_today'], 0, ',', ' ') }}</p>
                            <p class="text-xs text-gray-500">FCFA</p>
                        </div>
                    </div>
                @endif
            @endcan

            <!-- Produits en stock -->
            <div class="group relative bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/5 to-pink-500/5 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                            <i data-lucide="package" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">En stock</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ $stats['total_products_in_stock'] }}</p>
                    @if($stats['products_low_stock'] > 0)
                    <div class="flex items-center gap-1 text-amber-600">
                        <i data-lucide="alert-triangle" class="w-3 h-3 sm:w-3.5 sm:h-3.5 flex-shrink-0"></i>
                        <span class="text-xs sm:text-sm font-medium truncate">{{ $stats['products_low_stock'] }} bas</span>
                    </div>
                    @else
                    <p class="text-xs sm:text-sm text-emerald-600 font-medium">Tout OK</p>
                    @endif
                </div>
            </div>

            <!-- Chez revendeurs -->
            <div class="group relative bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-orange-500/5 rounded-xl sm:rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <div class="relative">
                    <div class="mb-3 sm:mb-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-lg sm:rounded-xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                            <i data-lucide="users" class="w-4 h-4 sm:w-5 sm:h-5 text-white"></i>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Revendeurs</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 sm:mb-2">{{ $stats['products_chez_revendeur'] }}</p>
                    <p class="text-xs text-gray-500">En attente</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Graphique des ventes -->
            <div class="lg:col-span-2 bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-0 mb-4 sm:mb-6">
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">Évolution des ventes</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">Sur {{ $period }} jours</p>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="$set('period', '7')"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-medium transition-all duration-200 rounded-lg
                                       {{ $period == '7' ? 'text-white bg-indigo-600 shadow-lg shadow-indigo-600/30' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                            7J
                        </button>
                        <button wire:click="$set('period', '30')"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-medium transition-all duration-200 rounded-lg
                                       {{ $period == '30' ? 'text-white bg-indigo-600 shadow-lg shadow-indigo-600/30' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                            30J
                        </button>
                        <button wire:click="$set('period', '90')"
                                class="flex-1 sm:flex-none px-3 py-1.5 text-xs font-medium transition-all duration-200 rounded-lg
                                       {{ $period == '90' ? 'text-white bg-indigo-600 shadow-lg shadow-indigo-600/30' : 'text-gray-700 bg-gray-100 hover:bg-gray-200' }}">
                            90J
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-4 sm:mb-6">
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-indigo-50 to-indigo-100/50 rounded-lg sm:rounded-xl">
                        <p class="text-xs font-medium text-indigo-600 mb-1">Total ventes</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900">{{ $stats['sales_month'] }}</p>
                    </div>
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-lg sm:rounded-xl">
                        <p class="text-xs font-medium text-emerald-600 mb-1">Chiffre d'affaires</p>
                        <p class="text-base sm:text-2xl font-bold text-gray-900 truncate">{{ number_format($stats['revenue_month'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">FCFA</p>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <div class="p-3 sm:p-4 bg-gradient-to-br from-purple-50 to-purple-100/50 rounded-lg sm:rounded-xl mb-4 sm:mb-6">
                        <p class="text-xs font-medium text-purple-600 mb-1">Bénéfice total</p>
                        <p class="text-base sm:text-2xl font-bold text-gray-900 truncate">{{ number_format($stats['profit_month'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">FCFA</p>
                    </div>
                @endif

                <!-- ApexCharts -->
                <div class="relative h-64 sm:h-72" wire:ignore>
                    <div id="salesChart"></div>
                </div>
            </div>

            <!-- Alertes stock -->
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Alertes stock</h3>
                    @if($lowStockProducts->count() > 0)
                    <span class="w-5 h-5 sm:w-6 sm:h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ $lowStockProducts->count() }}
                    </span>
                    @endif
                </div>

                <div class="space-y-2 sm:space-y-3 max-h-80 sm:max-h-96 overflow-y-auto">
                    @if($lowStockProducts->count() > 0)
                        @foreach($lowStockProducts as $productModel)
                        <div class="group p-2.5 sm:p-3 bg-gradient-to-br from-amber-50 to-orange-50/50 rounded-lg sm:rounded-xl border border-amber-200/50 hover:shadow-md transition-all duration-200">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-amber-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $productModel->name }}</p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Stock: {{ $productModel->computed_stock_quantity ?? 0 }} / Min: {{ $productModel->stock_minimum }}
                                    </p>
                                    @if($productModel->brand)
                                    <span class="text-xs text-gray-500 hidden sm:inline">{{ $productModel->brand }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 sm:py-12">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <i data-lucide="check-circle" class="w-6 h-6 sm:w-8 sm:h-8 text-emerald-600"></i>
                            </div>
                            <p class="text-xs sm:text-sm font-medium text-gray-900 mb-1">Tout va bien !</p>
                            <p class="text-xs text-gray-500">Aucune alerte stock</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Sales & Pending -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
            <!-- Ventes récentes -->
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">Ventes récentes</h3>
                    <a href="{{ route('sales.index') }}" class="text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-1 group">
                        <span class="hidden sm:inline">Voir tout</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>

                <div class="space-y-2">
                    @forelse($recentSales as $sale)
                    <a href="{{ route('sales.show', $sale) }}" class="block p-3 sm:p-4 rounded-lg sm:rounded-xl hover:bg-gray-50 transition-all duration-200 group border border-transparent hover:border-gray-200">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="shopping-bag" class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                                        <span class="hidden sm:inline">{{ $sale->seller->name }} • </span>{{ $sale->date_vente_effective->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs sm:text-sm font-bold text-gray-900">{{ number_format($sale->prix_vente, 0, ',', ' ') }}</p>
                                @if(auth()->user()->isAdmin())
                                    <span class="inline-flex items-center px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 mt-1">
                                        +{{ number_format($sale->benefice, 0, ',', ' ') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="text-center py-8 sm:py-12">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <i data-lucide="inbox" class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400"></i>
                        </div>
                        <p class="text-xs sm:text-sm text-gray-500">Aucune vente récente</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Ventes revendeurs en attente -->
            @if($pendingSales && $pendingSales->count() > 0)
            <div class="bg-white rounded-xl sm:rounded-2xl border border-gray-200 p-4 sm:p-6 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300">
                <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900">En attente</h3>
                    <span class="inline-flex items-center px-2 sm:px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 flex-shrink-0">
                        {{ $pendingSales->count() }}
                    </span>
                </div>

                <div class="space-y-2">
                    @foreach($pendingSales as $sale)
                    <div class="p-3 sm:p-4 rounded-lg sm:rounded-xl bg-gradient-to-br from-amber-50 to-orange-50/50 border border-amber-200/50 hover:shadow-md transition-all duration-200">
                        <div class="flex items-center justify-between gap-3 sm:gap-4">
                            <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-amber-100 rounded-lg sm:rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5 text-amber-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                                    <p class="text-xs text-gray-600 mt-0.5 truncate">
                                        <span class="hidden sm:inline">{{ $sale->reseller->name }} • </span>{{ $sale->date_depot_revendeur->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            <form action="{{ route('sales.confirm', $sale) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                <button type="submit" class="px-3 sm:px-4 py-1.5 sm:py-2 bg-gradient-to-br from-emerald-600 to-emerald-700 hover:from-emerald-700 hover:to-emerald-800 text-white text-xs font-medium rounded-lg sm:rounded-xl transition-all duration-200 shadow-lg shadow-emerald-600/30 hover:shadow-emerald-600/40 flex items-center gap-1 sm:gap-1.5 whitespace-nowrap">
                                    <i data-lucide="check" class="w-3 h-3 sm:w-3.5 sm:h-3.5"></i>
                                    <span class="hidden xs:inline">OK</span>
                                    <span class="hidden sm:inline">Confirmer</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        let chartInstance = null;

        document.addEventListener('DOMContentLoaded', function() {
            initializeChart();
        });

        Livewire.on('chartDataUpdated', () => {
            initializeChart();
        });

        function initializeChart() {
            const chartData = @json($chartData);
            const chartLabels = @json($chartLabels);
            const isMobile = window.innerWidth < 640;

            const options = {
                series: [{
                    name: 'Ventes',
                    data: chartData.sales
                }, {
                    name: isMobile ? 'CA' : 'Chiffre d\'affaires',
                    data: chartData.revenue
                }
                @if(auth()->user()->isAdmin())
                , {
                    name: 'Bénéfice',
                    data: chartData.profit
                }
                @endif
                ],
                chart: {
                    height: isMobile ? 256 : 320,
                    type: 'area',
                    toolbar: {
                        show: !isMobile,
                        tools: {
                            download: true,
                            selection: false,
                            zoom: true,
                            zoomin: true,
                            zoomout: true,
                            pan: false,
                            reset: true
                        }
                    },
                    zoom: { enabled: true },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: isMobile ? [2, 2, 2] : [3, 2, 2]
                },
                colors: ['#6366f1', '#10b981', '#8b5cf6'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        opacityFrom: 0.7,
                        opacityTo: 0.1,
                    }
                },
                xaxis: {
                    categories: chartLabels,
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: isMobile ? '9px' : '11px'
                        },
                        rotate: isMobile ? -45 : 0,
                        rotateAlways: isMobile,
                        hideOverlappingLabels: true
                    }
                },
                yaxis: [{
                    title: {
                        text: isMobile ? 'Ventes' : 'Nombre de ventes',
                        style: {
                            color: '#6366f1',
                            fontSize: isMobile ? '10px' : '12px',
                            fontWeight: 500
                        }
                    },
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: isMobile ? '9px' : '11px'
                        }
                    }
                }, {
                    opposite: true,
                    title: {
                        text: 'FCFA',
                        style: {
                            color: '#10b981',
                            fontSize: isMobile ? '10px' : '12px',
                            fontWeight: 500
                        }
                    },
                    labels: {
                        formatter: function(value) {
                            if (isMobile && value >= 1000) {
                                return (value / 1000).toFixed(0) + 'k';
                            }
                            return Math.round(value).toLocaleString('fr-FR');
                        },
                        style: {
                            colors: '#6B7280',
                            fontSize: isMobile ? '9px' : '11px'
                        }
                    }
                }],
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontSize: isMobile ? '10px' : '12px',
                    fontWeight: 500,
                    markers: {
                        width: isMobile ? 8 : 10,
                        height: isMobile ? 8 : 10,
                        radius: 10
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: {
                        formatter: function(value, { seriesIndex }) {
                            if (seriesIndex === 0) {
                                return value + ' vente(s)';
                            }
                            return value.toLocaleString('fr-FR') + ' FCFA';
                        }
                    },
                    style: {
                        fontSize: isMobile ? '10px' : '12px'
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 3
                }
            };

            const existingChart = document.querySelector("#salesChart");
            if (existingChart) {
                existingChart.innerHTML = '';
            }

            if (chartInstance) {
                chartInstance.destroy();
            }

            chartInstance = new ApexCharts(document.querySelector("#salesChart"), options);
            chartInstance.render();
        }

        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                initializeChart();
            }, 250);
        });
    </script>
    @endpush
</div>