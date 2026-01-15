<div class="space-y-8" x-data="{
    achat: {{ old('prix_revient_default', $productModel->prix_revient_default ?? 0) }},
    vente: {{ old('prix_vente_default', $productModel->prix_vente_default ?? 0) }},
    get marge() {
        return this.vente - this.achat;
    },
    get pourcentage() {
        if (this.achat === 0) return 0;
        return ((this.marge / this.achat) * 100).toFixed(1);
    }
}">
    {{-- Section: Informations générales --}}
    <div>
        <div class="mb-4 pb-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Informations générales</h3>
            <p class="text-sm text-gray-500 mt-1">Détails du modèle de produit</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <x-input-label for="name" value="Nom du modèle *" class="font-medium" />
                <x-text-input
                    type="text"
                    name="name"
                    id="name"
                    :value="old('name', $productModel->name ?? '')"
                    class="mt-2 block w-full"
                    placeholder="Ex: iPhone 15 Pro Max 256GB"
                    required
                />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="brand" value="Marque *" class="font-medium" />
                <select
                    name="brand"
                    id="brand"
                    required
                    class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
                >
                    <option value="">Sélectionner une marque</option>
                    @php
                        $brands = [
                            'Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Oppo', 'Vivo',
                            'Realme', 'OnePlus', 'Google', 'Nokia', 'Motorola', 'Sony',
                            'Asus', 'Lenovo', 'HP', 'Dell', 'Acer', 'Microsoft', 'LG', 'Anker', 'Générique'
                        ];
                    @endphp
                    @foreach($brands as $brand)
                        <option value="{{ $brand }}" {{ old('brand', $productModel->brand ?? '') == $brand ? 'selected' : '' }}>
                            {{ $brand }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('brand')" class="mt-2" />

                {{-- Option pour ajouter une marque personnalisée --}}
                <div class="mt-3" x-data="{ customBrand: false }">
                    <button
                        type="button"
                        @click="customBrand = !customBrand; if(customBrand) { document.getElementById('brand').value = ''; }"
                        class="text-sm text-gray-600 hover:text-gray-900 underline"
                    >
                        + Ajouter une autre marque
                    </button>
                    <div x-show="customBrand" x-cloak class="mt-2">
                        <x-text-input
                            type="text"
                            id="custom_brand"
                            @input="document.getElementById('brand').value = $event.target.value"
                            placeholder="Entrez le nom de la marque"
                            class="block w-full"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6">
            <x-input-label for="category" value="Catégorie *" class="font-medium" />
            <select
                name="category"
                id="category"
                required
                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >
                <option value="">Sélectionner une catégorie</option>
                <option value="telephone" {{ old('category', $productModel->category ?? '') == 'telephone' ? 'selected' : '' }}>
                    📱 Téléphone
                </option>
                <option value="tablette" {{ old('category', $productModel->category ?? '') == 'tablette' ? 'selected' : '' }}>
                    💻 Tablette
                </option>
                <option value="pc" {{ old('category', $productModel->category ?? '') == 'pc' ? 'selected' : '' }}>
                    🖥️ Ordinateur
                </option>
                <option value="accessoire" {{ old('category', $productModel->category ?? '') == 'accessoire' ? 'selected' : '' }}>
                    🎧 Accessoire
                </option>
            </select>
            <x-input-error :messages="$errors->get('category')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-input-label for="description" value="Description" class="font-medium" />
            <textarea
                name="description"
                id="description"
                rows="4"
                placeholder="Caractéristiques principales, points forts du produit..."
                class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900"
            >{{ old('description', $productModel->description ?? '') }}</textarea>
            <p class="mt-2 text-sm text-gray-500">Optionnel - Ajoutez une description pour faciliter l'identification</p>
            <x-input-error :messages="$errors->get('description')" class="mt-2" />
        </div>
    </div>

    {{-- Section: Tarification --}}
    <div>
        <div class="mb-4 pb-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Tarification</h3>
            <p class="text-sm text-gray-500 mt-1">Prix par défaut appliqués aux nouveaux produits</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <x-input-label for="prix_revient_default" value="Prix d'achat par défaut *" class="font-medium" />
                <div class="relative mt-2">
                    <x-text-input
                        type="number"
                        name="prix_revient_default"
                        id="prix_revient_default"
                        :value="old('prix_revient_default', $productModel->prix_revient_default ?? '')"
                        class="block w-full pr-16"
                        min="0"
                        step="100"
                        placeholder="0"
                        required
                        x-model.number="achat"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm font-medium">FCFA</span>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Coût d'acquisition du produit</p>
                <x-input-error :messages="$errors->get('prix_revient_default')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="prix_vente_default" value="Prix de vente par défaut *" class="font-medium" />
                <div class="relative mt-2">
                    <x-text-input
                        type="number"
                        name="prix_vente_default"
                        id="prix_vente_default"
                        :value="old('prix_vente_default', $productModel->prix_vente_default ?? '')"
                        class="block w-full pr-16"
                        min="0"
                        step="100"
                        placeholder="0"
                        required
                        x-model.number="vente"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 text-sm font-medium">FCFA</span>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500">Prix proposé aux clients</p>
                <x-input-error :messages="$errors->get('prix_vente_default')" class="mt-2" />
            </div>
        </div>

        {{-- Marge calculée --}}
        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700">Marge prévisionnelle</p>
                    <p class="text-xs text-gray-500 mt-0.5">Calculée automatiquement</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-semibold text-gray-900" x-text="marge.toLocaleString('fr-FR') + ' FCFA'"></p>
                    <p class="text-sm text-gray-600" x-text="'(' + pourcentage + '%)'"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Section: Gestion du stock --}}
    <div>
        <div class="mb-4 pb-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Gestion du stock</h3>
            <p class="text-sm text-gray-500 mt-1">Paramètres d'alerte de stock</p>
        </div>

        <div>
            <x-input-label for="stock_minimum" value="Seuil d'alerte de stock *" class="font-medium" />
            <x-text-input
                type="number"
                name="stock_minimum"
                id="stock_minimum"
                :value="old('stock_minimum', $productModel->stock_minimum ?? 5)"
                class="mt-2 block w-full max-w-xs"
                min="0"
                placeholder="5"
                required
            />
            <p class="mt-2 text-sm text-gray-500">
                <span class="inline-flex items-center gap-1">
                    <i data-lucide="info" class="w-4 h-4"></i>
                    Une alerte sera affichée si le stock descend en dessous de cette valeur
                </span>
            </p>
            <x-input-error :messages="$errors->get('stock_minimum')" class="mt-2" />
        </div>
    </div>

    {{-- Section: Statut --}}
    <div>
        <div class="mb-4 pb-3 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Statut</h3>
            <p class="text-sm text-gray-500 mt-1">Disponibilité du modèle</p>
        </div>

        <div class="flex items-start">
            <div class="flex items-center h-5">
                <input
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    value="1"
                    {{ old('is_active', $productModel->is_active ?? true) ? 'checked' : '' }}
                    class="w-4 h-4 rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                >
            </div>
            <div class="ml-3">
                <label for="is_active" class="font-medium text-sm text-gray-900 cursor-pointer">
                    Modèle actif
                </label>
                <p class="text-sm text-gray-500 mt-1">
                    Les modèles inactifs ne seront pas proposés lors de l'ajout de nouveaux produits
                </p>
            </div>
        </div>
        <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
        <a
            href="{{ route('product-models.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 transition-colors"
        >
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Annuler
        </a>

        <button
            type="submit"
            class="inline-flex items-center gap-2 px-6 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 transition-colors"
        >
            <i data-lucide="save" class="w-4 h-4"></i>
            {{ isset($productModel) ? 'Mettre à jour le modèle' : 'Créer le modèle' }}
        </button>
    </div>
</div>
