<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
        <!-- Header -->
        <div class="border-b border-gray-200 bg-white/80 backdrop-blur-xl sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 sm:py-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Journal d'Activité</h1>
                        <p class="mt-1 text-sm text-gray-500">Suivi en temps réel des actions de votre équipe</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600 bg-green-50 px-3 py-2 rounded-lg border border-green-200">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="font-medium">En direct</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
            @php
                $lastDate = null;
                
                // Traductions des actions
                $actionLabels = [
                    'created' => 'a ajouté',
                    'updated' => 'a modifié',
                    'deleted' => 'a supprimé',
                    'restored' => 'a restauré',
                ];

                // Traductions des types
                $typeLabels = [
                    'Product' => 'le produit',
                    'Sale' => 'la vente',
                    'User' => 'l\'utilisateur',
                    'Reseller' => 'le revendeur',
                    'TradeIn' => 'le troc',
                    'StockMovement' => 'le mouvement de stock',
                    'CustomerReturn' => 'le retour',
                    'ProductModel' => 'le modèle',
                    'Payment' => 'le paiement',
                ];

                // Champs à traduire et formater
                $fieldLabels = [
                    // IDs
                    'id' => 'ID',
                    'product_id' => 'Produit',
                    'sale_id' => 'Vente',
                    'user_id' => 'Utilisateur',
                    'reseller_id' => 'Revendeur',
                    
                    // Montants
                    'prix_vente' => 'Prix de vente',
                    'prix_achat_produit' => 'Prix d\'achat',
                    'benefice' => 'Bénéfice',
                    'amount' => 'Montant',
                    'amount_paid' => 'Montant payé',
                    'amount_remaining' => 'Reste à payer',
                    
                    // États
                    'state' => 'État',
                    'state_before' => 'État initial',
                    'state_after' => 'Nouvel état',
                    'location' => 'Localisation',
                    'location_before' => 'Localisation initiale',
                    'location_after' => 'Nouvelle localisation',
                    'payment_status' => 'Statut paiement',
                    
                    // Autres
                    'type' => 'Type',
                    'quantity' => 'Quantité',
                    'client_name' => 'Client',
                    'client_phone' => 'Téléphone',
                    'payment_method' => 'Mode de paiement',
                    'notes' => 'Notes',
                    'is_confirmed' => 'Confirmé',
                    'sale_type' => 'Type de vente',
                ];

                // Champs à masquer (non pertinents pour l'utilisateur)
                $hiddenFields = [
                    'created_at', 'updated_at', 'deleted_at', 
                    'updated_by', 'sold_by', 'recorded_by',
                    'date_depot_revendeur', 'date_confirmation_vente',
                    'date_vente_effective', 'payment_date', 'final_payment_date', 'payment_due_date',
                    'related_product_id', 'justification'
                ];

                // Champs importants à mettre en avant
                $highlightFields = ['benefice', 'state', 'location', 'amount_paid', 'amount_remaining', 'payment_status'];
            @endphp

            <div class="space-y-4">
                @forelse($activities as $activity)
                    @php
                        $date = $activity->created_at->format('Y-m-d');
                        $showDateHeader = $date !== $lastDate;
                        $lastDate = $date;

                        $actionLabel = $actionLabels[$activity->event] ?? 'a effectué une action sur';
                        $subjectType = class_basename($activity->subject_type);
                        $typeLabel = $typeLabels[$subjectType] ?? 'l\'élément';
                    @endphp

                    @if($showDateHeader)
                        <div class="flex items-center gap-3 mt-8 first:mt-0">
                            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-3 py-1 bg-white rounded-full border border-gray-200">
                                {{ $activity->created_at->translatedFormat('l j F Y') }}
                            </span>
                            <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
                        </div>
                    @endif

                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-sm hover:shadow-md transition-all duration-300 group">
                        <div class="flex items-start gap-3 sm:gap-4">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                @if($activity->causer)
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center text-white font-semibold shadow-sm ring-2 ring-white group-hover:ring-gray-100 transition-all">
                                        {{ substr($activity->causer->name, 0, 1) }}
                                    </div>
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 ring-2 ring-white">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <!-- Main action -->
                                <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-1 mb-2">
                                    <p class="text-sm sm:text-base text-gray-900">
                                        <span class="font-semibold text-gray-900">
                                            {{ $activity->causer ? $activity->causer->name : 'Système' }}
                                        </span>
                                        <span class="text-gray-600 mx-1">{{ $actionLabel }}</span>
                                        <span class="font-medium text-gray-800">{{ $typeLabel }}</span>
                                        @if($activity->subject_id)
                                            <code class="ml-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs font-mono rounded border border-gray-200">
                                                #{{ $activity->subject_id }}
                                            </code>
                                        @endif
                                    </p>
                                    <span class="text-xs text-gray-500 whitespace-nowrap flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Changes details -->
                                @if($activity->properties && $activity->properties->count() > 0)
                                    @php
                                        $attributes = $activity->properties['attributes'] ?? [];
                                        $old = $activity->properties['old'] ?? [];
                                        
                                        // Filtrer les champs à afficher
                                        $visibleAttributes = array_filter(
                                            $attributes,
                                            fn($key) => !in_array($key, $hiddenFields),
                                            ARRAY_FILTER_USE_KEY
                                        );
                                        
                                        // Séparer les champs importants
                                        $importantChanges = [];
                                        $regularChanges = [];
                                        
                                        foreach($visibleAttributes as $key => $value) {
                                            $change = [
                                                'key' => $key,
                                                'label' => $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                                                'value' => $value,
                                                'old' => $old[$key] ?? null,
                                            ];
                                            
                                            if(in_array($key, $highlightFields)) {
                                                $importantChanges[] = $change;
                                            } else {
                                                $regularChanges[] = $change;
                                            }
                                        }
                                    @endphp

                                    @if(count($importantChanges) > 0 || count($regularChanges) > 0)
                                        <div class="space-y-2 mt-3">
                                            <!-- Changements importants -->
                                            @if(count($importantChanges) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($importantChanges as $change)
                                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-200 rounded-lg">
                                                            <span class="text-xs font-semibold text-blue-900">{{ $change['label'] }}</span>
                                                            @if($change['old'] !== null && $change['old'] != $change['value'])
                                                                <span class="text-xs text-blue-700 line-through opacity-60">
                                                                    {{ is_bool($change['old']) ? ($change['old'] ? 'Oui' : 'Non') : $change['old'] }}
                                                                </span>
                                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                                </svg>
                                                            @endif
                                                            <span class="text-xs font-bold text-blue-900">
                                                                @if(is_bool($change['value']))
                                                                    {{ $change['value'] ? 'Oui' : 'Non' }}
                                                                @elseif(is_numeric($change['value']) && in_array($change['key'], ['benefice', 'prix_vente', 'prix_achat_produit', 'amount', 'amount_paid', 'amount_remaining']))
                                                                    {{ number_format($change['value'], 0, ',', ' ') }} FCFA
                                                                @else
                                                                    {{ $change['value'] }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <!-- Autres changements -->
                                            @if(count($regularChanges) > 0)
                                                <details class="group/details">
                                                    <summary class="cursor-pointer text-xs text-gray-500 hover:text-gray-700 flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 transition-transform group-open/details:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                        <span>Voir {{ count($regularChanges) }} autre(s) modification(s)</span>
                                                    </summary>
                                                    <div class="mt-2 pl-5 flex flex-wrap gap-2">
                                                        @foreach($regularChanges as $change)
                                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-gray-200 rounded text-xs">
                                                                <span class="font-medium text-gray-700">{{ $change['label'] }}:</span>
                                                                <span class="text-gray-900 max-w-[200px] truncate">
                                                                    @if(is_bool($change['value']))
                                                                        {{ $change['value'] ? 'Oui' : 'Non' }}
                                                                    @elseif(is_numeric($change['value']) && strlen($change['value']) > 6)
                                                                        {{ number_format($change['value'], 0, ',', ' ') }}
                                                                    @else
                                                                        {{ $change['value'] }}
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </details>
                                            @endif
                                        </div>
                                    @endif
                                @endif

                                <!-- Description si présente -->
                                @if($activity->description && is_string($activity->description))
                                    <p class="mt-2 text-xs text-gray-500 italic">{{ $activity->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">Aucune activité enregistrée</p>
                        <p class="text-sm text-gray-400 mt-1">Les actions apparaîtront ici en temps réel</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($activities->hasPages())
                <div class="mt-6">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>