<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResellerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('resellers.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+229)?[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
                'unique:resellers,phone',
            ],
            'phone_secondary' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+229)?[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}[\s\-]?[0-9]{2}$/',
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'phone' => 'téléphone principal',
            'phone_secondary' => 'téléphone secondaire',
            'address' => 'adresse',
            'notes' => 'notes',
            'is_active' => 'actif',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du revendeur est obligatoire.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'phone.regex' => 'Le format du numéro de téléphone n\'est pas valide. Format attendu : +229 XX XX XX XX ou XX XX XX XX',
            'phone.unique' => 'Ce numéro de téléphone est déjà enregistré.',
            'phone_secondary.regex' => 'Le format du numéro de téléphone secondaire n\'est pas valide.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Nettoyer les numéros de téléphone
        if ($this->has('phone')) {
            $this->merge([
                'phone' => $this->cleanPhoneNumber($this->phone),
            ]);
        }

        if ($this->has('phone_secondary') && $this->phone_secondary) {
            $this->merge([
                'phone_secondary' => $this->cleanPhoneNumber($this->phone_secondary),
            ]);
        }

        // Définir is_active par défaut si non fourni
        if (!$this->has('is_active')) {
            $this->merge([
                'is_active' => true,
            ]);
        }

        // Nettoyer les espaces dans les champs texte
        if ($this->has('name')) {
            $this->merge([
                'name' => trim($this->name),
            ]);
        }
    }

    /**
     * Nettoyer un numéro de téléphone
     */
    private function cleanPhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Garder uniquement les chiffres et le +
        $cleaned = preg_replace('/[^0-9+]/', '', $phone);

        // Si commence par 00229, remplacer par +229
        if (str_starts_with($cleaned, '00229')) {
            $cleaned = '+229' . substr($cleaned, 5);
        }

        // Si commence par 229 sans +, ajouter le +
        if (str_starts_with($cleaned, '229') && !str_starts_with($cleaned, '+')) {
            $cleaned = '+' . $cleaned;
        }

        // Si ne commence pas par + et fait 8 chiffres, ajouter +229
        if (!str_starts_with($cleaned, '+') && strlen($cleaned) === 8) {
            $cleaned = '+229' . $cleaned;
        }

        return $cleaned;
    }
}
