<?php

namespace App\Http\Requests;

use App\Enums\StockMovementType;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStockMovementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Vérifier les permissions selon le type de mouvement
        $type = StockMovementType::tryFrom($this->type);

        if (!$type) {
            return false;
        }

        // Les vendeurs peuvent seulement faire des ventes et retours clients
        if ($this->user()->isVendeur()) {
            return in_array($type, StockMovementType::forVendeur());
        }

        // Les admins peuvent tout faire
        return $this->user()->can('stock.view');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $type = StockMovementType::tryFrom($this->type);

        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],

            'type' => [
                'required',
                'string',
                Rule::enum(StockMovementType::class),
            ],

            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],

            'status_after' => ['required', 'string'],

            // Relations optionnelles
            'sale_id' => [
                Rule::requiredIf(fn() => $type && in_array($type, [
                    StockMovementType::VENTE_DIRECTE,
                    StockMovementType::VENTE_TROC,
                ])),
                'nullable',
                'integer',
                'exists:sales,id',
            ],

            'reseller_id' => [
                Rule::requiredIf(fn() => $type && $type->requiresResellerInfo()),
                'nullable',
                'integer',
                'exists:resellers,id',
            ],

            'related_product_id' => [
                'nullable',
                'integer',
                'exists:products,id',
                'different:product_id',
            ],

            // Justification
            'justification' => [
                Rule::requiredIf(fn() => $type && $type->requiresJustification()),
                'nullable',
                'string',
                'min:10',
                'max:2000',
            ],

            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_id' => 'produit',
            'type' => 'type de mouvement',
            'quantity' => 'quantité',
            'status_after' => 'statut après mouvement',
            'sale_id' => 'vente',
            'reseller_id' => 'revendeur',
            'related_product_id' => 'produit lié',
            'justification' => 'justification',
            'notes' => 'notes',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est obligatoire.',
            'product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'type.required' => 'Le type de mouvement est obligatoire.',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.min' => 'La quantité doit être au minimum de 1.',
            'status_after.required' => 'Le statut après mouvement est obligatoire.',
            'sale_id.required' => 'La vente associée est obligatoire pour ce type de mouvement.',
            'reseller_id.required' => 'Le revendeur est obligatoire pour ce type de mouvement.',
            'related_product_id.different' => 'Le produit lié doit être différent du produit principal.',
            'justification.required' => 'Une justification est obligatoire pour ce type de mouvement.',
            'justification.min' => 'La justification doit contenir au moins 10 caractères.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Récupérer le statut actuel du produit
        if ($this->product_id) {
            $product = Product::find($this->product_id);
            if ($product) {
                $this->merge([
                    'status_before' => $product->status->value,
                ]);
            }
        }
    }

    /**
     * Get data to be validated from the request (after preparation).
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ajouter l'utilisateur
        $validated['user_id'] = $this->user()->id;

        // Ajouter le status_before
        $validated['status_before'] = $this->status_before ?? null;

        return $validated;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validation personnalisée : vérifier que le produit peut subir ce mouvement
            if ($this->product_id && $this->type) {
                $product = Product::find($this->product_id);
                $type = StockMovementType::tryFrom($this->type);

                if ($product && $type) {
                    // Exemple : ne pas vendre un produit déjà vendu
                    if (in_array($type, [StockMovementType::VENTE_DIRECTE, StockMovementType::VENTE_TROC])) {
                        if (!$product->status->isAvailable()) {
                            $validator->errors()->add(
                                'product_id',
                                'Ce produit n\'est pas disponible à la vente (statut: ' . $product->status->label() . ').'
                            );
                        }
                    }

                    // Ne pas faire de dépôt revendeur sur un produit déjà chez un revendeur
                    if ($type === StockMovementType::DEPOT_REVENDEUR) {
                        if ($product->status->value === \App\Enums\ProductStatus::CHEZ_REVENDEUR->value) {
                            $validator->errors()->add(
                                'product_id',
                                'Ce produit est déjà chez un revendeur.'
                            );
                        }
                    }
                }
            }
        });
    }
}
