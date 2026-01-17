<?php

namespace App\Http\Requests;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Models\ProductModel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('products.create');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_model_id' => ['required', 'integer', 'exists:product_models,id'],
            'imei' => [
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{15}$/',
                'unique:products,imei',
                Rule::requiredIf(function () {
                    // IMEI requis si le modèle est un téléphone
                    if ($this->product_model_id) {
                        $model = ProductModel::find($this->product_model_id);

                        return $model && $model->category->value === 'telephone';
                    }

                    return false;
                }),
            ],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'state' => [
                'required',
                'string',
                Rule::enum(ProductState::class),
            ],
            'location' => [
                'required',
                'string',
                Rule::enum(ProductLocation::class),
            ],
            'prix_achat' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'prix_vente' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'gte:prix_achat'],
            'date_achat' => ['nullable', 'date', 'before_or_equal:today'],
            'fournisseur' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'condition' => ['nullable', 'string', Rule::in(['Neuf', 'Excellent', 'Très bon', 'Bon', 'Correct', 'Passable'])],
            'defauts' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_model_id' => 'modèle de produit',
            'imei' => 'IMEI',
            'serial_number' => 'numéro de série',
            'state' => 'état',
            'location' => 'localisation',
            'prix_achat' => 'prix d\'achat',
            'prix_vente' => 'prix de vente',
            'date_achat' => 'date d\'achat',
            'fournisseur' => 'fournisseur',
            'notes' => 'notes',
            'condition' => 'condition',
            'defauts' => 'défauts',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_model_id.required' => 'Le modèle de produit est obligatoire.',
            'product_model_id.exists' => 'Le modèle de produit sélectionné n\'existe pas.',
            'imei.required' => 'L\'IMEI est obligatoire pour les téléphones.',
            'imei.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
            'imei.regex' => 'L\'IMEI doit contenir uniquement des chiffres.',
            'imei.unique' => 'Cet IMEI est déjà enregistré dans le système.',
            'state.required' => 'L\'état est obligatoire.',
            'state.enum' => 'L\'état sélectionné n\'est pas valide.',
            'location.required' => 'La localisation est obligatoire.',
            'location.enum' => 'La localisation sélectionnée n\'est pas valide.',
            'prix_achat.required' => 'Le prix d\'achat est obligatoire.',
            'prix_vente.required' => 'Le prix de vente est obligatoire.',
            'prix_vente.gte' => 'Le prix de vente doit être supérieur ou égal au prix d\'achat.',
            'date_achat.before_or_equal' => 'La date d\'achat ne peut pas être dans le futur.',
            'condition.in' => 'La condition sélectionnée n\'est pas valide.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer l'IMEI (retirer espaces et tirets)
        if ($this->has('imei') && $this->imei) {
            $this->merge([
                'imei' => preg_replace('/[^0-9]/', '', $this->imei),
            ]);
        }

        // Définir l'état par défaut si non fourni
        if (! $this->has('state')) {
            $this->merge([
                'state' => ProductState::DISPONIBLE->value,
            ]);
        }

        // Définir la localisation par défaut si non fournie
        if (! $this->has('location')) {
            $this->merge([
                'location' => ProductLocation::BOUTIQUE->value,
            ]);
        }

        // Définir la date d'achat par défaut si non fournie
        if (! $this->has('date_achat')) {
            $this->merge([
                'date_achat' => now()->format('Y-m-d'),
            ]);
        }

        // Ajouter l'ID de l'utilisateur créateur
        $this->merge([
            'created_by' => $this->user()->id,
        ]);
    }

    /**
     * Get data to be validated from the request (after preparation).
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ajouter created_by au validated data
        $validated['created_by'] = $this->user()->id;

        return $validated;
    }

    /**
     * Validation conditionnelle pour la cohérence état/localisation
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $state = $this->input('state');
            $location = $this->input('location');

            // Vérifier la cohérence lors de la création
            if ($state && $location) {
                $this->validateStateLocationConsistency($validator, $state, $location);
            }
        });
    }

    /**
     * Valider la cohérence entre état et localisation
     */
    private function validateStateLocationConsistency($validator, string $state, string $location): void
    {
        // Un nouveau produit ne devrait pas être vendu à la création
        if ($state === ProductState::VENDU->value) {
            $validator->errors()->add(
                'state',
                'Un nouveau produit ne peut pas être créé avec l\'état "Vendu". Utilisez le processus de vente.'
            );
        }

        // Un nouveau produit ne devrait pas être chez le client
        if ($location === ProductLocation::CHEZ_CLIENT->value) {
            $validator->errors()->add(
                'location',
                'Un nouveau produit ne peut pas être créé avec la localisation "Chez client". Utilisez le processus de vente.'
            );
        }

        // Vérifications de cohérence standard
        $invalidCombinations = [
            [
                'state' => ProductState::A_REPARER->value,
                'invalid_locations' => [ProductLocation::CHEZ_CLIENT->value],
                'message' => 'Un produit à réparer ne peut pas être chez le client.',
            ],
            [
                'state' => ProductState::PERDU->value,
                'invalid_locations' => [
                    ProductLocation::BOUTIQUE->value,
                    ProductLocation::CHEZ_REVENDEUR->value,
                    ProductLocation::CHEZ_CLIENT->value,
                ],
                'message' => 'Un produit perdu ne peut pas avoir une localisation active.',
            ],
        ];

        foreach ($invalidCombinations as $rule) {
            if ($state === $rule['state'] && in_array($location, $rule['invalid_locations'])) {
                $validator->errors()->add('location', $rule['message']);
                break;
            }
        }
    }
}
