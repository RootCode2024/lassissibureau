<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('products.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return [
            'product_model_id' => ['sometimes', 'required', 'integer', 'exists:product_models,id'],
            'imei' => [
                'nullable',
                'string',
                'size:15',
                'regex:/^[0-9]{15}$/',
                Rule::unique('products', 'imei')->ignore($productId),
            ],
            'serial_number' => ['nullable', 'string', 'max:100'],
            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::enum(ProductStatus::class),
            ],
            'prix_achat' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'prix_vente' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99', 'gte:prix_achat'],
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
            'status' => 'statut',
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
            'imei.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
            'imei.regex' => 'L\'IMEI doit contenir uniquement des chiffres.',
            'imei.unique' => 'Cet IMEI est déjà enregistré dans le système.',
            'status.required' => 'Le statut est obligatoire.',
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

        // Ajouter l'ID de l'utilisateur modificateur
        $this->merge([
            'updated_by' => $this->user()->id,
        ]);
    }

    /**
     * Get data to be validated from the request (after preparation).
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Ajouter updated_by au validated data
        $validated['updated_by'] = $this->user()->id;

        return $validated;
    }
}
