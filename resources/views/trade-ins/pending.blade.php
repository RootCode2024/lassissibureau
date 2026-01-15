<x-app-layout>
    <x-slot name="header">
        Trocs en attente de traitement
    </x-slot>

    <div class="space-y-6">
        @if($tradeIns->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($tradeIns as $tradeIn)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="bg-purple-600 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-white">Vente #{{ $tradeIn->sale_id }}</span>
                                <span class="text-xs text-white/80">{{ $tradeIn->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="p-6">
                            {{-- Produit vendu --}}
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-xs text-gray-500 uppercase tracking-wide mb-2">Produit vendu</p>
                                <p class="text-sm font-medium text-gray-900">{{ $tradeIn->sale->product->productModel->name }}</p>
                                <p class="text-xs text-gray-500">{{ $tradeIn->sale->product->productModel->brand }}</p>
                            </div>

                            {{-- Produit reçu --}}
                            <div class="space-y-3 mb-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Produit reçu en troc</p>

                                <div>
                                    <p class="text-xs text-gray-500">Modèle</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $tradeIn->modele_recu }}</p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">IMEI</p>
                                    <p class="text-sm font-mono text-gray-900">{{ $tradeIn->imei_recu }}</p>
                                </div>

                                @if($tradeIn->etat_recu)
                                    <div>
                                        <p class="text-xs text-gray-500">État</p>
                                        <p class="text-sm text-gray-700">{{ $tradeIn->etat_recu }}</p>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                                    <span class="text-xs text-purple-700 font-medium">Valeur de reprise</span>
                                    <span class="text-lg font-bold text-purple-900">{{ number_format($tradeIn->valeur_reprise, 0, ',', ' ') }} FCFA</span>
                                </div>
                            </div>

                            {{-- Vendeur --}}
                            <div class="mb-4 text-xs text-gray-500">
                                Par {{ $tradeIn->sale->seller->name }}
                            </div>

                            {{-- Action --}}
                            <a href="{{ route('trade-ins.create-product', $tradeIn) }}" class="block w-full text-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition-colors">
                                Créer le produit
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                {{ $tradeIns->links() }}
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg p-12 text-center">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-4"></i>
                <p class="text-sm text-gray-500">Aucun troc en attente</p>
                <p class="text-xs text-gray-400 mt-2">Tous les trocs ont été traités</p>
            </div>
        @endif
    </div>
</x-app-layout>
