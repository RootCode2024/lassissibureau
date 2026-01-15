<x-app-layout>
    <x-slot name="header">
        Rapport de Ventes
    </x-slot>

    <div class="space-y-6">
        {{-- Sélecteur de période avec bouton PDF --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <form method="GET" action="{{ route('reports.daily') }}" class="space-y-4">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label for="start_date" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline-block mr-2"></i>
                            Date de début
                        </label>
                        <input 
                            type="date" 
                            id="start_date" 
                            name="start_date" 
                            value="{{ $startDate }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm"
                        >
                    </div>

                    <div class="flex-1">
                        <label for="end_date" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            <i data-lucide="calendar" class="w-4 h-4 inline-block mr-2"></i>
                            Date de fin
                        </label>
                        <input 
                            type="date" 
                            id="end_date" 
                            name="end_date" 
                            value="{{ $endDate }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600 text-sm"
                        >
                    </div>

                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Afficher
                    </button>

                    <a href="{{ route('reports.download-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        PDF
                    </a>
                </div>

                {{-- Raccourcis --}}
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-gray-500">Raccourcis:</span>
                    <button type="button" onclick="setToday()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">Aujourd'hui</button>
                    <button type="button" onclick="setLast7Days()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">7 derniers jours</button>
                    <button type="button" onclick="setLast30Days()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">30 derniers jours</button>
                    <button type="button" onclick="setThisMonth()" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded transition-colors">Ce mois</button>
                </div>
            </form>
        </div>

        {{-- En-tête du rapport --}}
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        @if($startDate === $endDate)
                            Rapport du {{ \Carbon\Carbon::parse($startDate)->isoFormat('dddd D MMMM YYYY') }}
                        @else
                            Rapport du {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM YYYY') }} au {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM YYYY') }}
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        @php
                            $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                        @endphp
                        {{ $days }} jour(s) - {{ $report['stats']['total_sales'] }} vente(s)
                    </p>
                </div>
            </div>
        </div>

        {{-- Statistiques principales --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Ventes</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $report['stats']['total_sales'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Chiffre d'affaires</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($report['stats']['total_revenue'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-500">FCFA</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Bénéfices</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($report['stats']['total_profit'], 0, ',', ' ') }}</p>
                        <p class="text-xs text-gray-500">FCFA</p>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="dollar-sign" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Sorties revendeurs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $report['stats']['total_reseller_sales'] }}</p>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Détail des ventes --}}
        @if(count($report['sales']) > 0)
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-900">Détail des ventes ({{ count($report['sales']) }})</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date & Heure</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">IMEI/Série</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client/Revendeur</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Prix</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Bénéfice</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach($report['sales'] as $sale)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $sale->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $sale->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $sale->product->productModel->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $sale->product->productModel->brand }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                                        {{ $sale->product->imei ?: ($sale->product->serial_number ?: 'N/A') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        @if($sale->reseller)
                                            <span class="inline-flex items-center px-2 py-1 bg-amber-100 text-amber-800 text-xs font-medium rounded">
                                                {{ $sale->reseller->name }}
                                            </span>
                                        @else
                                            {{ $sale->client_name ?? 'Client direct' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($sale->prix_vente, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-green-600">
                                        +{{ number_format($sale->benefice, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                                @if($sale->tradeIn)
                                    <tr class="bg-purple-50">
                                        <td colspan="6" class="px-6 py-3">
                                            <div class="flex items-center gap-2 text-xs text-purple-900">
                                                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                                <strong>Troc:</strong> {{ $sale->tradeIn->modele_recu }} (IMEI: {{ $sale->tradeIn->imei_recu }}) -
                                                Reprise: <strong>{{ number_format($sale->tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</strong> -
                                                Complément: <strong>{{ number_format($sale->tradeIn->complement_especes, 0, ',', ' ') }} FCFA</strong>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                <p class="text-sm text-gray-500">Aucune vente enregistrée pour cette période</p>
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
