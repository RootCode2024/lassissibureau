<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use App\Enums\SaleType;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('sales.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Produit vendu
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    $product = Product::find($value);
                    if ($product && !$product->status->isAvailable()) {
                        $fail('Ce produit n\'est pas disponible à la vente (statut: ' . $product->status->label() . ').');
                    }
                },
            ],

            // Type de vente
            'sale_type' => [
                'required',
                'string',
                Rule::enum(SaleType::class),
            ],

            // Prix
            'prix_vente' => ['required', 'numeric', 'min:0', 'max:99999999.99'],

            // Informations client
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+229)?[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
            ],

            // Revendeur (optionnel)
            'reseller_id' => ['nullable', 'integer', 'exists:resellers,id'],
            'date_depot_revendeur' => [
                Rule::requiredIf(fn() => $this->reseller_id !== null),
                'nullable',
                'date',
                'before_or_equal:today',
            ],

            'notes' => ['nullable', 'string', 'max:2000'],

            // Troc (si sale_type = TROC ou TROC_AVEC_COMPLEMENT)
            'has_trade_in' => ['boolean'],
            'trade_in.product_model_name' => [
                Rule::requiredIf(fn() => $this->has_trade_in === true),
                'nullable',
                'string',
                'max:255',
            ],
            'trade_in.imei_recu' => [
                Rule::requiredIf(fn() => $this->has_trade_in === true),
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{15}$/',
                'unique:products,imei',
            ],
            'trade_in.valeur_reprise' => [
                Rule::requiredIf(fn() => $this->has_trade_in === true),
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'trade_in.complement_especes' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
            ],
            'trade_in.etat_recu' => ['nullable', 'string', 'max:500'],
            'trade_in.condition' => [
                'nullable',
                'string',
                Rule::in(['Neuf', 'Excellent', 'Très bon', 'Bon', 'Correct', 'Passable', 'Mauvais']),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produit',
            'sale_type' => 'type de vente',
            'prix_vente' => 'prix de vente',
            'client_name' => 'nom du client',
            'client_phone' => 'téléphone du client',
            'reseller_id' => 'revendeur',
            'date_depot_revendeur' => 'date de dépôt',
            'notes' => 'notes',
            'trade_in.product_model_name' => 'modèle du téléphone repris',
            'trade_in.imei_recu' => 'IMEI du téléphone repris',
            'trade_in.valeur_reprise' => 'valeur de reprise',
            'trade_in.complement_especes' => 'complément en espèces',
            'trade_in.etat_recu' => 'état du téléphone repris',
            'trade_in.condition' => 'condition',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Vous devez sélectionner un produit.',
            'product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'sale_type.required' => 'Le type de vente est obligatoire.',
            'prix_vente.required' => 'Le prix de vente est obligatoire.',
            'client_phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
            'reseller_id.exists' => 'Le revendeur sélectionné n\'existe pas.',
            'date_depot_revendeur.required' => 'La date de dépôt est obligatoire pour une vente via revendeur.',
            'trade_in.product_model_name.required' => 'Le modèle du téléphone repris est obligatoire.',
            'trade_in.imei_recu.required' => 'L\'IMEI du téléphone repris est obligatoire.',
            'trade_in.imei_recu.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
            'trade_in.imei_recu.regex' => 'L\'IMEI doit contenir uniquement des chiffres.',
            'trade_in.imei_recu.unique' => 'Cet IMEI est déjà enregistré dans le système.',
            'trade_in.valeur_reprise.required' => 'La valeur de reprise est obligatoire.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer le téléphone client
        if ($this->has('client_phone') && $this->client_phone) {
            $this->merge([
                'client_phone' => $this->cleanPhoneNumber($this->client_phone),
            ]);
        }

        // Nettoyer l'IMEI du troc
        if ($this->has('trade_in.imei_recu') && $this->input('trade_in.imei_recu')) {
            $tradeIn = $this->input('trade_in');
            $tradeIn['imei_recu'] = preg_replace('/[^0-9]/', '', $tradeIn['imei_recu']);
            $this->merge(['trade_in' => $tradeIn]);
        }

        // Définir le complément par défaut à 0 si non fourni
        if ($this->has_trade_in && !$this->has('trade_in.complement_especes')) {
            $tradeIn = $this->input('trade_in', []);
            $tradeIn['complement_especes'] = 0;
            $this->merge(['trade_in' => $tradeIn]);
        }

        // Définir has_trade_in basé sur le sale_type
        if ($this->has('sale_type')) {
            $saleType = SaleType::tryFrom($this->sale_type);
            if ($saleType && in_array($saleType, [SaleType::TROC, SaleType::TROC_AVEC_COMPLEMENT])) {
                $this->merge(['has_trade_in' => true]);
            }
        }

        // Date effective de vente
        if ($this->reseller_id) {
            // Si revendeur, la date effective est la date de dépôt
            $this->merge([
                'date_vente_effective' => $this->date_depot_revendeur ?? now()->format('Y-m-d'),
                'is_confirmed' => false, // En attente de confirmation
            ]);
        } else {
            // Sinon, c'est aujourd'hui
            $this->merge([
                'date_vente_effective' => now()->format('Y-m-d'),
                'is_confirmed' => true,
            ]);
        }
    }

    /**
     * Get data to be validated from the request (after preparation).
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Récupérer le produit pour avoir son prix d'achat
        $product = Product::findOrFail($this->product_id);

        // Ajouter les données calculées
        $validated['prix_achat_produit'] = $product->prix_achat;
        $validated['sold_by'] = $this->user()->id;
        $validated['date_vente_effective'] = $this->date_vente_effective;
        $validated['is_confirmed'] = $this->is_confirmed;

        return $validated;
    }

    /**
     * Nettoyer un numéro de téléphone
     */
    private function cleanPhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($cleaned, '00229')) {
            $cleaned = '+229' . substr($cleaned, 5);
        }

        if (str_starts_with($cleaned, '229') && !str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }

        if (!str_starts_with($cleaned, '+') && strlen($cleaned) === 8) {
            $cleaned = '+229' . $cleaned;
        }

        return $cleaned;
    }
}
