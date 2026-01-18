<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-4">
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold text-lg sm:text-xl text-gray-900">Rapport de Ventes</h2>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 sm:space-y-6">
        {{-- Sélecteur de période --}}
        <div class="bg-gradient-to-br from-white to-blue-50 border border-blue-200 rounded-xl p-4 sm:p-6 shadow-sm">
            <form method="GET" action="{{ route('reports.daily') }}" class="space-y-3 sm:space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="start_date" class="flex items-center gap-1.5 text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            Date de début
                        </label>
                        <input 
                            type="date" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ $startDate }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="block w-full py-2 rounded-lg border-blue-300 shadow-sm focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm"
                        >
                    </div>

                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="end_date" class="flex items-center gap-1.5 text-xs font-semibold text-blue-700 uppercase tracking-wide mb-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            Date de fin
                        </label>
                        <input 
                            type="date" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ $endDate }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="block w-full py-2 rounded-lg border-blue-300 shadow-sm focus:border-blue-600 focus:ring-1 focus:ring-blue-600 text-sm"
                        >
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 active:bg-blue-800 transition-all shadow-md hover:shadow-lg">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            <span>Afficher</span>
                        </button>
                    </div>

                    {{-- ✅ Bouton PDF - VISIBLE UNIQUEMENT POUR ADMIN --}}
                    @if($canViewProfits)
                    <div class="flex items-end gap-2">
                        <a href="{{ route('reports.download-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 active:bg-red-800 transition-all shadow-md hover:shadow-lg">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            <span>PDF</span>
                        </a>
                        
                        <button type="submit" form="export-sales-form" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 active:bg-green-800 transition-all shadow-md hover:shadow-lg">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                            <span>Excel</span>
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Hidden Export Form --}}
                {{-- Raccourcis --}}
                <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-blue-200">
                    <span class="text-xs font-medium text-blue-700">Raccourcis:</span>
                    <button type="button" onclick="setToday()" class="px-2.5 sm:px-3 py-1 bg-white hover:bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 rounded-lg transition-colors">Aujourd'hui</button>
                    <button type="button" onclick="setLast7Days()" class="px-2.5 sm:px-3 py-1 bg-white hover:bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 rounded-lg transition-colors">7 jours</button>
                    <button type="button" onclick="setLast30Days()" class="px-2.5 sm:px-3 py-1 bg-white hover:bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 rounded-lg transition-colors">30 jours</button>
                    <button type="button" onclick="setThisMonth()" class="px-2.5 sm:px-3 py-1 bg-white hover:bg-blue-50 text-blue-700 text-xs font-medium border border-blue-200 rounded-lg transition-colors">Ce mois</button>
                </div>
            </form>
            
            {{-- Hidden Export Form --}}
            @if($canViewProfits)
            <form id="export-sales-form" method="POST" action="{{ route('reports.export.sales') }}" class="hidden">
                @csrf
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            </form>
            @endif
            {{-- End Hidden Form --}}
        </div>

        {{-- En-tête du rapport --}}
        <div class="bg-gradient-to-r from-gray-900 to-gray-800 border border-gray-700 rounded-xl p-4 sm:p-6 shadow-lg">
            <div>
                <h3 class="text-base sm:text-lg font-bold text-white">
                    @if($startDate === $endDate)
                        Rapport du {{ \Carbon\Carbon::parse($startDate)->isoFormat('dddd D MMMM YYYY') }}
                    @else
                        Rapport du {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM') }} au {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}
                    @endif
                </h3>
                <p class="text-xs sm:text-sm text-gray-300 mt-1">
                    @php
                        $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                    @endphp
                    <span class="inline-flex items-center gap-1">
                        <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                        {{ $days }} jour(s)
                    </span>
                    <span class="mx-2 text-gray-500">•</span>
                    <span class="inline-flex items-center gap-1">
                        <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i>
                        {{ $report['stats']['total_sales'] }} vente(s)
                    </span>
                </p>
            </div>
        </div>

        {{-- Statistiques principales --}}
        <div class="grid grid-cols-2 lg:grid-cols-{{ $canViewProfits ? '4' : '3' }} gap-2 sm:gap-3 lg:gap-4">
            <div class="bg-gradient-to-br from-white to-blue-50 border border-blue-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] sm:text-xs font-semibold text-blue-600 uppercase tracking-wide mb-1">Ventes</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-black text-blue-700 leading-tight">{{ $report['stats']['total_sales'] }}</p>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="shopping-cart" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-white to-green-50 border border-green-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] sm:text-xs font-semibold text-green-600 uppercase tracking-wide mb-1">CA</p>
                        <p class="text-base sm:text-xl lg:text-2xl font-black text-green-700 break-all leading-tight">{{ number_format($report['stats']['total_revenue'], 0, ',', ' ') }}</p>
                        <p class="text-[10px] sm:text-xs text-green-600 font-medium mt-0.5">FCFA</p>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="trending-up" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                    </div>
                </div>
            </div>

            {{-- ✅ Carte Bénéfices - VISIBLE UNIQUEMENT POUR ADMIN --}}
            @if($canViewProfits)
            <div class="bg-gradient-to-br from-white to-purple-50 border border-purple-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] sm:text-xs font-semibold text-purple-600 uppercase tracking-wide mb-1">Bénéfices</p>
                        <p class="text-base sm:text-xl lg:text-2xl font-black text-purple-700 break-all leading-tight">{{ number_format($report['stats']['total_profit'], 0, ',', ' ') }}</p>
                        <p class="text-[10px] sm:text-xs text-purple-600 font-medium mt-0.5">FCFA</p>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="dollar-sign" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-gradient-to-br from-white to-amber-50 border border-amber-200 rounded-xl p-3 sm:p-4 lg:p-6 shadow-sm hover:shadow-md transition-all {{ $canViewProfits ? '' : 'col-span-2 lg:col-span-1' }}">
                <div class="flex items-center justify-between gap-2">
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] sm:text-xs font-semibold text-amber-600 uppercase tracking-wide mb-1">Revendeurs</p>
                        <p class="text-xl sm:text-2xl lg:text-3xl font-black text-amber-700 leading-tight">{{ $report['stats']['total_reseller_sales'] }}</p>
                    </div>
                    <div class="w-9 h-9 sm:w-10 sm:h-10 lg:w-12 lg:h-12 bg-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="users" class="w-4 h-4 sm:w-5 sm:h-5 lg:w-6 lg:h-6 text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Détail des ventes --}}
        @if(count($report['sales']) > 0)
            {{-- Vue Desktop (Tableau) --}}
            <div class="hidden lg:block bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h4 class="text-sm font-semibold text-gray-900">Détail des ventes ({{ count($report['sales']) }})</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date & Heure</th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produit</th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">IMEI/Série</th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client/Revendeur</th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Vendeur</th>
                                <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix</th>
                                {{-- ✅ Colonne Bénéfice - VISIBLE UNIQUEMENT POUR ADMIN --}}
                                @if($canViewProfits)
                                <th scope="col" class="px-4 xl:px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Bénéfice</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($report['sales'] as $sale)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 xl:px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $sale->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $sale->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4">
                                        <div class="min-w-0">
                                            <div class="text-sm font-medium text-gray-900 truncate">{{ $sale->product->productModel->name }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ $sale->product->productModel->brand }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4 text-sm text-gray-900 font-mono truncate">
                                        {{ $sale->product->imei ?: ($sale->product->serial_number ?: 'N/A') }}
                                    </td>
                                    <td class="px-4 xl:px-6 py-4">
                                        @if($sale->reseller)
                                            <span class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded border border-amber-200">
                                                {{ $sale->reseller->name }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-900 truncate">{{ $sale->client_name ?? 'Client direct' }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 xl:px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $sale->seller->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 xl:px-6 py-4 text-right text-sm font-semibold text-gray-900 whitespace-nowrap">
                                        {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                                    </td>
                                    {{-- ✅ Colonne Bénéfice - VISIBLE UNIQUEMENT POUR ADMIN --}}
                                    @if($canViewProfits)
                                    <td class="px-4 xl:px-6 py-4 text-right text-sm font-semibold text-green-600 whitespace-nowrap">
                                        +{{ number_format($sale->benefice, 0, ',', ' ') }} FCFA
                                    </td>
                                    @endif
                                </tr>
                                @if($sale->tradeIn)
                                    <tr class="bg-purple-50">
                                        <td colspan="{{ $canViewProfits ? '7' : '6' }}" class="px-4 xl:px-6 py-3">
                                            <div class="flex items-center gap-2 text-xs text-purple-900">
                                                <i data-lucide="refresh-cw" class="w-3 h-3 flex-shrink-0"></i>
                                                <span><strong>Troc:</strong> {{ $sale->tradeIn->modele_recu }} ({{ $sale->tradeIn->imei_recu }}) - Reprise: <strong>{{ number_format($sale->tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</strong> - Complément: <strong>{{ number_format($sale->tradeIn->complement_especes, 0, ',', ' ') }} FCFA</strong></span>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Vue Mobile/Tablet (Cards) --}}
            <div class="lg:hidden">
                <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4 mb-3 shadow-sm">
                    <h4 class="text-sm font-semibold text-gray-900">Détail des ventes ({{ count($report['sales']) }})</h4>
                </div>
                <div class="space-y-3">
                    @foreach($report['sales'] as $sale)
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            {{-- Header --}}
                            <div class="flex items-start justify-between gap-3 mb-3 pb-3 border-b border-gray-100">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $sale->product->productModel->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $sale->product->productModel->brand }}</p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        <span class="text-xs text-gray-600">{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-base font-bold text-gray-900">{{ number_format($sale->prix_vente / 1000, 0) }}k</p>
                                    {{-- ✅ Bénéfice - VISIBLE UNIQUEMENT POUR ADMIN --}}
                                    @if($canViewProfits)
                                    <p class="text-xs font-semibold text-green-600">+{{ number_format($sale->benefice / 1000, 0) }}k</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Info --}}
                            <div class="space-y-2">
                                <div class="flex items-start gap-2">
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500">IMEI/Série</p>
                                        <p class="text-sm font-mono text-gray-900 truncate">{{ $sale->product->imei ?: ($sale->product->serial_number ?: 'N/A') }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-2">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500">Client/Revendeur</p>
                                        @if($sale->reseller)
                                            <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-amber-800 text-xs font-medium rounded border border-amber-200 mt-0.5">
                                                {{ $sale->reseller->name }}
                                            </span>
                                        @else
                                            <p class="text-sm text-gray-900 truncate">{{ $sale->client_name ?? 'Client direct' }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-start gap-2">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-gray-500">Vendu par</p>
                                        <p class="text-sm text-gray-900 truncate">{{ $sale->seller->name ?? 'N/A' }}</p>
                                    </div>
                                </div>

                                @if($sale->tradeIn)
                                    <div class="p-2.5 bg-purple-50 border border-purple-200 rounded-lg">
                                        <div class="flex items-start gap-2 text-xs text-purple-900">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"></i>
                                            <div>
                                                <p class="font-semibold mb-0.5">Troc avec reprise</p>
                                                <p>{{ $sale->tradeIn->modele_recu }} ({{ $sale->tradeIn->imei_recu }})</p>
                                                <p class="mt-1">Reprise: <strong>{{ number_format($sale->tradeIn->valeur_reprise / 1000, 0) }}k</strong> - Complément: <strong>{{ number_format($sale->tradeIn->complement_especes / 1000, 0) }}k FCFA</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-xl p-8 sm:p-12 text-center shadow-sm">
                <div class="w-12 h-12 sm:w-16 sm:h-16 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i data-lucide="inbox" class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Aucune vente enregistrée</p>
                <p class="text-xs text-gray-500 mt-1">pour cette période</p>
            </div>
        @endif
    </div>

    <script>
        function setToday() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').value = today;
            document.getElementById('end_date').value = today;
        }

        function setLast7Days() {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - 6);
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
            document.getElementById('end_date').value = end.toISOString().split('T')[0];
        }

        function setLast30Days() {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - 29);
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
            document.getElementById('end_date').value = end.toISOString().split('T')[0];
        }

        function setThisMonth() {
            const now = new Date();
            const start = new Date(now.getFullYear(), now.getMonth(), 1);
            document.getElementById('start_date').value = start.toISOString().split('T')[0];
            document.getElementById('end_date').value = now.toISOString().split('T')[0];
        }
    </script>
</x-app-layout>