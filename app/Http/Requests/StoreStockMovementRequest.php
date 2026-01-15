<?php

namespace App\Http\Requests;

use App\Enums\StockMovementType;
use App\Enums\ProductState;
use App\Enums\ProductLocation;
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

            // État et localisation après mouvement
            'state_after' => [
                'required',
                'string',
                Rule::enum(ProductState::class),
            ],

            'location_after' => [
                'required',
                'string',
                Rule::enum(ProductLocation::class),
            ],

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
            'state_after' => 'état après mouvement',
            'location_after' => 'localisation après mouvement',
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
            'state_after.required' => 'L\'état après mouvement est obligatoire.',
            'state_after.enum' => 'L\'état après mouvement n\'est pas valide.',
            'location_after.required' => 'La localisation après mouvement est obligatoire.',
            'location_after.enum' => 'La localisation après mouvement n\'est pas valide.',
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
        // Récupérer l'état et la localisation actuels du produit
        if ($this->product_id) {
            $product = Product::find($this->product_id);
            if ($product) {
                $this->merge([
                    'state_before' => $product->state->value,
                    'location_before' => $product->location->value,
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

        // Ajouter state_before et location_before
        $validated['state_before'] = $this->state_before ?? null;
        $validated['location_before'] = $this->location_before ?? null;

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
                    $this->validateMovementAllowed($validator, $product, $type);
                    $this->validateStateLocationConsistency($validator);
                }
            }
        });
    }

    /**
     * Valider que le mouvement est autorisé pour ce produit
     */
    private function validateMovementAllowed($validator, Product $product, StockMovementType $type): void
    {
        // Ne pas vendre un produit qui n'est pas disponible
        if (in_array($type, [StockMovementType::VENTE_DIRECTE, StockMovementType::VENTE_TROC])) {
            if (!$product->isAvailable()) {
                $validator->errors()->add(
                    'product_id',
                    sprintf(
                        'Ce produit n\'est pas disponible à la vente (état: %s, localisation: %s).',
                        $product->state->label(),
                        $product->location->label()
                    )
                );
            }
        }

        // Ne pas déposer chez revendeur un produit déjà chez un revendeur
        if ($type === StockMovementType::DEPOT_REVENDEUR) {
            if ($product->location === ProductLocation::CHEZ_REVENDEUR) {
                $validator->errors()->add(
                    'product_id',
                    'Ce produit est déjà chez un revendeur.'
                );
            }
        }

        // Ne pas retourner d'un revendeur un produit qui n'est pas chez un revendeur
        if ($type === StockMovementType::RETOUR_REVENDEUR) {
            if ($product->location !== ProductLocation::CHEZ_REVENDEUR) {
                $validator->errors()->add(
                    'product_id',
                    'Ce produit n\'est pas chez un revendeur.'
                );
            }
        }

        // Ne pas envoyer en réparation un produit déjà vendu
        if ($type === StockMovementType::ENVOI_REPARATION) {
            if ($product->state === ProductState::VENDU) {
                $validator->errors()->add(
                    'product_id',
                    'Ce produit est déjà vendu et ne peut pas être envoyé en réparation.'
                );
            }
        }

        // Ne pas retourner de réparation un produit qui n'est pas en réparation
        if ($type === StockMovementType::RETOUR_REPARATION) {
            if (
                $product->location !== ProductLocation::EN_REPARATION &&
                $product->state !== ProductState::A_REPARER
            ) {
                $validator->errors()->add(
                    'product_id',
                    'Ce produit n\'est pas en réparation.'
                );
            }
        }
    }

    /**
     * Valider la cohérence entre état et localisation après mouvement
     */
    private function validateStateLocationConsistency($validator): void
    {
        $stateAfter = $this->input('state_after');
        $locationAfter = $this->input('location_after');

        if (!$stateAfter || !$locationAfter) {
            return;
        }

        $invalidCombinations = [
            // Un produit vendu ne peut pas être en boutique ou en réparation
            [
                'state' => ProductState::VENDU->value,
                'invalid_locations' => [
                    ProductLocation::BOUTIQUE->value,
                    ProductLocation::EN_REPARATION->value,
                    ProductLocation::FOURNISSEUR->value,
                ],
                'message' => 'Un produit vendu doit être chez le client ou chez un revendeur (vente non confirmée).'
            ],
            // Un produit à réparer ne peut pas être chez le client
            [
                'state' => ProductState::A_REPARER->value,
                'invalid_locations' => [
                    ProductLocation::CHEZ_CLIENT->value,
                    ProductLocation::FOURNISSEUR->value,
                ],
                'message' => 'Un produit à réparer doit être en boutique ou en réparation.'
            ],
            // Un produit disponible ne peut pas être chez le client
            [
                'state' => ProductState::DISPONIBLE->value,
                'invalid_locations' => [
                    ProductLocation::CHEZ_CLIENT->value,
                ],
                'message' => 'Un produit disponible ne peut pas être chez le client.'
            ],
        ];

        foreach ($invalidCombinations as $rule) {
            if ($stateAfter === $rule['state'] && in_array($locationAfter, $rule['invalid_locations'])) {
                $validator->errors()->add('location_after', $rule['message']);
                break;
            }
        }
    }
}
