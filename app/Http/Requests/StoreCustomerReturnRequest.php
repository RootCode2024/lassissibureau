<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('returns.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Vente et produit retourné
            'original_sale_id' => [
                'required',
                'integer',
                'exists:sales,id',
                function ($attribute, $value, $fail) {
                    $sale = Sale::find($value);
                    if ($sale && $sale->product->status->value !== ProductStatus::VENDU->value) {
                        $fail('Ce produit n\'est pas marqué comme vendu. Statut actuel: ' . $sale->product->status->label());
                    }
                },
            ],

            'returned_product_id' => [
                'required',
                'integer',
                'exists:products,id',
                function ($attribute, $value, $fail) {
                    // Vérifier que le produit retourné correspond bien à la vente
                    if ($this->original_sale_id) {
                        $sale = Sale::find($this->original_sale_id);
                        if ($sale && $sale->product_id != $value) {
                            $fail('Le produit retourné ne correspond pas à la vente sélectionnée.');
                        }
                    }
                },
            ],

            // Raison du retour
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'defect_description' => ['nullable', 'string', 'max:2000'],

            // Type de retour : échange ou remboursement
            'is_exchange' => ['required', 'boolean'],

            // Si échange
            'exchange_product_id' => [
                Rule::requiredIf(fn() => $this->is_exchange === true || $this->is_exchange === '1'),
                'nullable',
                'integer',
                'exists:products,id',
                'different:returned_product_id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $product = Product::find($value);
                        if ($product && !$product->status->isAvailable()) {
                            $fail('Le produit d\'échange sélectionné n\'est pas disponible (statut: ' . $product->status->label() . ').');
                        }
                    }
                },
            ],

            // Si remboursement
            'refund_amount' => [
                Rule::requiredIf(fn() => $this->is_exchange === false || $this->is_exchange === '0'),
                'nullable',
                'numeric',
                'min:0',
                'max:99999999.99',
                function ($attribute, $value, $fail) {
                    // Vérifier que le montant du remboursement ne dépasse pas le prix de vente
                    if ($this->original_sale_id && $value) {
                        $sale = Sale::find($this->original_sale_id);
                        if ($sale && $value > $sale->prix_vente) {
                            $fail('Le montant du remboursement ne peut pas dépasser le prix de vente original (' . number_format($sale->prix_vente, 0, ',', ' ') . ' FCFA).');
                        }
                    }
                },
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'original_sale_id' => 'vente originale',
            'returned_product_id' => 'produit retourné',
            'reason' => 'raison du retour',
            'defect_description' => 'description du défaut',
            'is_exchange' => 'type de retour',
            'exchange_product_id' => 'produit d\'échange',
            'refund_amount' => 'montant du remboursement',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'original_sale_id.required' => 'La vente originale est obligatoire.',
            'original_sale_id.exists' => 'La vente sélectionnée n\'existe pas.',
            'returned_product_id.required' => 'Le produit retourné est obligatoire.',
            'returned_product_id.exists' => 'Le produit retourné n\'existe pas.',
            'reason.required' => 'La raison du retour est obligatoire.',
            'reason.min' => 'La raison du retour doit contenir au moins 10 caractères.',
            'is_exchange.required' => 'Vous devez indiquer s\'il s\'agit d\'un échange ou d\'un remboursement.',
            'exchange_product_id.required' => 'Le produit d\'échange est obligatoire.',
            'exchange_product_id.different' => 'Le produit d\'échange doit être différent du produit retourné.',
            'refund_amount.required' => 'Le montant du remboursement est obligatoire.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si refund_amount n'est pas fourni pour un échange, le mettre à 0
        if ($this->is_exchange && !$this->has('refund_amount')) {
            $this->merge([
                'refund_amount' => 0,
            ]);
        }
    }

    /**
     * Get data to be validated from the request (after preparation).
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ajouter l'utilisateur qui traite le retour
        $validated['processed_by'] = $this->user()->id;

        return $validated;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Vérifier qu'on ne peut pas retourner le même produit deux fois
            if ($this->original_sale_id) {
                $existingReturn = \App\Models\CustomerReturn::where('original_sale_id', $this->original_sale_id)->first();
                if ($existingReturn) {
                    $validator->errors()->add(
                        'original_sale_id',
                        'Un retour a déjà été enregistré pour cette vente.'
                    );
                }
            }

            // Si échange, vérifier que le nouveau produit a un prix
            if ($this->is_exchange && $this->exchange_product_id) {
                $exchangeProduct = Product::find($this->exchange_product_id);
                if ($exchangeProduct && !$exchangeProduct->prix_vente) {
                    $validator->errors()->add(
                        'exchange_product_id',
                        'Le produit d\'échange doit avoir un prix de vente défini.'
                    );
                }
            }
        });
    }
}
