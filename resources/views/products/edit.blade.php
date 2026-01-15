<x-app-layout>
    <x-slot name="header">
        Modifier le produit #{{ $product->id }}
    </x-slot>

        <x-slot name="actions">
        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Retour
        </a>
    </x-slot>

    <x-alerts.success :message="session('success')" />
    <x-alerts.error :message="session('error')" />

    <div class="max-w-7xl mx-auto">
        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Sélection du modèle --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="box" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Informations du modèle</h3>
                        <p class="text-xs text-gray-500">Sélectionnez le modèle de produit</p>
                    </div>
                </div>

                <div>
                    <label for="product_model_id" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Modèle de produit *
                    </label>
                    <select name="product_model_id" id="product_model_id" required
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                        <option value="">Sélectionner un modèle</option>
                        @foreach($productModels as $model)
                            <option value="{{ $model->id }}" {{ old('product_model_id', $product->product_model_id) == $model->id ? 'selected' : '' }}>
                                {{ $model->name }} - {{ $model->brand }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('product_model_id')" class="mt-2" />
                </div>
            </div>

            {{-- Identification --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="hash" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Identification</h3>
                        <p class="text-xs text-gray-500">IMEI et numéro de série</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="imei" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            IMEI
                        </label>
                        <input
                            type="text"
                            name="imei"
                            id="imei"
                            value="{{ old('imei', $product->imei) }}"
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-mono"
                            maxlength="15"
                            pattern="[0-9]{15}"
                            placeholder="123456789012345"
                        />
                        <p class="mt-1.5 text-xs text-gray-500">15 chiffres (requis pour les téléphones)</p>
                        <x-input-error :messages="$errors->get('imei')" class="mt-2" />
                    </div>

                    <div>
                        <label for="serial_number" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Numéro de série
                        </label>
                        <input
                            type="text"
                            name="serial_number"
                            id="serial_number"
                            value="{{ old('serial_number', $product->serial_number) }}"
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm font-mono"
                        />
                        <x-input-error :messages="$errors->get('serial_number')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- État et Localisation --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">État & Localisation</h3>
                        <p class="text-xs text-gray-500">Statut actuel du produit</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="state" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            État *
                        </label>
                        <select name="state" id="state" required
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            @foreach($states as $state)
                                <option value="{{ $state['value'] }}" {{ old('state', $product->state->value) == $state['value'] ? 'selected' : '' }}>
                                    {{ $state['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('state')" class="mt-2" />
                    </div>

                    <div>
                        <label for="location" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Localisation *
                        </label>
                        <select name="location" id="location" required
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            @foreach($locations as $location)
                                <option value="{{ $location['value'] }}" {{ old('location', $product->location->value) == $location['value'] ? 'selected' : '' }}>
                                    {{ $location['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('location')" class="mt-2" />
                    </div>
                </div>
            </div>

            {{-- Prix --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="coins" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Tarification</h3>
                        <p class="text-xs text-gray-500">Prix d'achat et de vente</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="prix_achat" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Prix d'achat (FCFA) *
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                name="prix_achat"
                                id="prix_achat"
                                value="{{ old('prix_achat', $product->prix_achat) }}"
                                class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                min="0"
                                step="1"
                                required
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs text-gray-500 font-medium">FCFA</span>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('prix_achat')" class="mt-2" />
                    </div>

                    <div>
                        <label for="prix_vente" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Prix de vente (FCFA) *
                        </label>
                        <div class="relative">
                            <input
                                type="number"
                                name="prix_vente"
                                id="prix_vente"
                                value="{{ old('prix_vente', $product->prix_vente) }}"
                                class="block w-full py-2.5 pr-16 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                min="0"
                                step="1"
                                required
                            />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-xs text-gray-500 font-medium">FCFA</span>
                            </div>
                        </div>
                        <p class="mt-1.5 text-xs text-gray-500">Doit être supérieur ou égal au prix d'achat</p>
                        <x-input-error :messages="$errors->get('prix_vente')" class="mt-2" />
                    </div>
                </div>

                {{-- Aperçu de la marge --}}
                <div id="margin-preview" class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Bénéfice</p>
                            <p id="benefice-value" class="text-lg font-bold text-gray-900">{{ number_format($product->benefice_potentiel, 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Marge</p>
                            <p id="marge-value" class="text-lg font-bold text-gray-900">{{ $product->marge_percentage }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Détails complémentaires --}}
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-lucide="info" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">Détails complémentaires</h3>
                        <p class="text-xs text-gray-500">Condition et informations d'achat</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="condition" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Condition
                        </label>
                        <select name="condition" id="condition"
                                class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">Sélectionner une condition</option>
                            @foreach($conditions as $condition)
                                <option value="{{ $condition }}" {{ old('condition', $product->condition) == $condition ? 'selected' : '' }}>
                                    {{ $condition }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                    </div>

                    <div>
                        <label for="date_achat" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                            Date d'achat
                        </label>
                        <input
                            type="date"
                            name="date_achat"
                            id="date_achat"
                            value="{{ old('date_achat', $product->date_achat?->format('Y-m-d')) }}"
                            class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                            max="{{ now()->format('Y-m-d') }}"
                        />
                        <x-input-error :messages="$errors->get('date_achat')" class="mt-2" />
                    </div>
                </div>

                <div class="mb-6">
                    <label for="fournisseur" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Fournisseur
                    </label>
                    <input
                        type="text"
                        name="fournisseur"
                        id="fournisseur"
                        value="{{ old('fournisseur', $product->fournisseur) }}"
                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                        placeholder="Nom du fournisseur"
                    />
                    <x-input-error :messages="$errors->get('fournisseur')" class="mt-2" />
                </div>

                <div class="mb-6">
                    <label for="defauts" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Défauts constatés
                    </label>
                    <textarea
                        name="defauts"
                        id="defauts"
                        rows="3"
                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                        placeholder="Décrivez les défauts ou problèmes du produit..."
                    >{{ old('defauts', $product->defauts) }}</textarea>
                    <x-input-error :messages="$errors->get('defauts')" class="mt-2" />
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">
                        Notes
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="3"
                        class="block w-full py-2.5 rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                        placeholder="Notes additionnelles..."
                    >{{ old('notes', $product->notes) }}</textarea>
                    <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between gap-4 bg-white border border-gray-200 rounded-lg p-6">
                <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-black border border-black rounded-md font-medium text-sm text-white hover:bg-gray-800 transition-colors">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Mettre à jour le produit
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calculate margin
            const prixAchatInput = document.getElementById('prix_achat');
            const prixVenteInput = document.getElementById('prix_vente');
            const marginPreview = document.getElementById('margin-preview');
            const beneficeValue = document.getElementById('benefice-value');
            const margeValue = document.getElementById('marge-value');
            const roiValue = document.getElementById('roi-value');

            prixAchatInput.addEventListener('input', calculateMargin);
            prixVenteInput.addEventListener('input', calculateMargin);

            function calculateMargin() {
                const prixAchat = parseFloat(prixAchatInput.value) || 0;
                const prixVente = parseFloat(prixVenteInput.value) || 0;

                if (prixAchat > 0 && prixVente > 0) {
                    const benefice = prixVente - prixAchat;
                    const marge = ((benefice / prixVente) * 100);
                    const roi = ((benefice / prixAchat) * 100);

                    beneficeValue.textContent = benefice.toLocaleString('fr-FR') + ' FCFA';
                    margeValue.textContent = marge.toFixed(2) + '%';
                    roiValue.textContent = roi.toFixed(2) + '%';

                    // Color code based on margin
                    if (marge >= 30) {
                        margeValue.classList.remove('text-gray-900', 'text-yellow-600');
                        margeValue.classList.add('text-green-600');
                    } else if (marge >= 15) {
                        margeValue.classList.remove('text-gray-900', 'text-green-600');
                        margeValue.classList.add('text-yellow-600');
                    } else {
                        margeValue.classList.remove('text-green-600', 'text-yellow-600');
                        margeValue.classList.add('text-gray-900');
                    }

                    marginPreview.classList.remove('hidden');
                } else {
                    marginPreview.classList.add('hidden');
                }
            }

            // Trigger on page load with existing values
            calculateMargin();
        });
    </script>
    @endpush
</x-app-layout>
