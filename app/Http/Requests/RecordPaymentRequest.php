<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:'.($this->route('sale')->amount_remaining ?? 999999),
            ],
            'payment_method' => ['required', 'string', 'in:cash,mobile_money,bank_transfer,check'],
            'payment_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Le montant du paiement est requis.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'amount.max' => 'Le montant ne peut pas dépasser le solde restant.',
            'payment_method.required' => 'La méthode de paiement est requise.',
            'payment_method.in' => 'La méthode de paiement sélectionnée est invalide.',
            'payment_date.required' => 'La date du paiement est requise.',
            'payment_date.before_or_equal' => 'La date du paiement ne peut pas être dans le futur.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sale = $this->route('sale');

            if (! $sale) {
                return;
            }

            // Vérifier que la vente peut recevoir des paiements
            if ($sale->isFullyPaid()) {
                $validator->errors()->add(
                    'amount',
                    'Cette vente est déjà entièrement payée.'
                );
            }

            // Vérifier que le montant ne dépasse pas le solde
            if ($this->amount > $sale->amount_remaining) {
                $validator->errors()->add(
                    'amount',
                    'Le montant ('.number_format($this->amount, 0, ',', ' ').' FCFA) dépasse le solde restant ('.number_format($sale->amount_remaining, 0, ',', ' ').' FCFA).'
                );
            }
        });
    }
}
