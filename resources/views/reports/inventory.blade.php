<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rapport d\'Inventaire') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">État complet du stock et valorisation</p>
            </div>
            <div>
                <form method="POST" action="{{ route('reports.export.inventory') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all shadow-md">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Exporter Excel
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Valorisation --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Valeur Achat Totale</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($report['total_value_cost'], 0, ',', ' ') }} FCFA</div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Valeur Vente Totale</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($report['total_value_sale'], 0, ',', ' ') }} FCFA</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-purple-500">
                    <div class="text-sm font-medium text-gray-500 uppercase">Bénéfice Potentiel</div>
                    <div class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($report['potential_profit'], 0, ',', ' ') }} FCFA</div>
                    <div class="mt-1 text-sm text-green-600">
                        @if($report['total_value_cost'] > 0)
                            Marge: {{ round(($report['potential_profit'] / $report['total_value_cost']) * 100, 1) }}%
                        @endif
                    </div>
                </div>
            </div>

            {{-- Répartition par État --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Répartition par État</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($report['by_state'] as $state => $data)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="font-semibold text-gray-700 mb-2">{{ \App\Enums\ProductState::from($state)->label() }}</div>
                                <div class="flex justify-between items-end">
                                    <div class="text-2xl font-bold">{{ $data['count'] }}</div>
                                    <div class="text-xs text-gray-500 text-right">
                                        <div>Val: {{ number_format($data['total_sale_value'], 0, ',', ' ') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Répartition par Localisation --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Répartition par Localisation</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($report['by_location'] as $location => $data)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="font-semibold text-gray-700 mb-2">{{ \App\Enums\ProductLocation::from($location)->label() }}</div>
                                <div class="flex justify-between items-end">
                                    <div class="text-2xl font-bold">{{ $data['count'] }}</div>
                                    <div class="text-xs text-gray-500 text-right">
                                        <div>Val: {{ number_format($data['total_sale_value'], 0, ',', ' ') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Détail par Modèle (Top 50) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Détail par Modèle</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modèle</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Potentiel Vente</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($report['by_model'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['model'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ $item['quantity'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($item['total_cost'], 0, ',', ' ') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-right">{{ number_format($item['total_sale_value'], 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
