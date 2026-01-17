<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Produit et type de vente
            'product_id' => ['required', 'exists:products,id'],
            'sale_type' => ['required', 'string', 'in:achat_direct,troc'],

            // Prix
            'prix_vente' => ['required', 'numeric', 'min:0'],
            'prix_achat_produit' => ['required', 'numeric', 'min:0'],

            // Client
            'client_name' => ['nullable', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:20'],

            // Type d'acheteur
            'buyer_type' => ['required', 'string', 'in:direct,reseller'],

            // Revendeur
            'reseller_id' => ['nullable', 'required_if:buyer_type,reseller', 'exists:resellers,id'],
            'date_depot_revendeur' => ['nullable', 'required_with:reseller_id', 'date'],

            // Paiement (pour revendeurs)
            'payment_status' => ['nullable', 'required_with:reseller_id', 'in:unpaid,partial,paid'],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'lte:prix_vente'],
            'payment_due_date' => ['nullable', 'required_with:reseller_id', 'date', 'after_or_equal:today'],
            'payment_method' => ['nullable', 'string', 'in:cash,mobile_money,bank_transfer,check'],

            // Dates
            'date_vente_effective' => ['required', 'date'],
            'is_confirmed' => ['required', 'boolean'],

            // Notes
            'notes' => ['nullable', 'string'],

            // Trade-in (validation conditionnelle)
            'has_trade_in' => ['nullable', 'boolean'],
            'trade_in.modele_recu' => [
                'nullable',
                'required_if:sale_type,troc',
                'string',
                'max:255',
            ],
            'trade_in.imei_recu' => [
                'nullable',
                'required_if:sale_type,troc',
                'string',
                'size:15',
            ],
            'trade_in.valeur_reprise' => [
                'nullable',
                'required_if:sale_type,troc',
                'numeric',
                'min:0',
                'lte:prix_vente',
            ],
            'trade_in.complement_especes' => ['nullable', 'numeric'],
            'trade_in.etat_recu' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Le produit est requis.',
            'product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'sale_type.required' => 'Le type de vente est requis.',
            'sale_type.in' => 'Le type de vente doit être "achat_direct" ou "troc".',
            'prix_vente.required' => 'Le prix de vente est requis.',
            'prix_vente.min' => 'Le prix de vente doit être positif.',
            'date_vente_effective.required' => 'La date de vente est requise.',
            'is_confirmed.required' => 'Le statut de confirmation est requis.',

            'buyer_type.required' => 'Le type d\'acheteur est requis.',
            'reseller_id.required_if' => 'Le revendeur est requis pour une vente revendeur.',
            'payment_status.required_with' => 'Le statut de paiement est requis pour une vente revendeur.',
            'amount_paid.lte' => 'Le montant payé ne peut pas dépasser le prix de vente.',
            'payment_due_date.after_or_equal' => 'La date d\'échéance ne peut pas être dans le passé.',

            'trade_in.modele_recu.required_if' => 'Le modèle reçu est requis pour un troc.',
            'trade_in.imei_recu.required_if' => 'L\'IMEI reçu est requis pour un troc.',
            'trade_in.imei_recu.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
            'trade_in.valeur_reprise.required_if' => 'La valeur de reprise est requise pour un troc.',
            'trade_in.valeur_reprise.min' => 'La valeur de reprise doit être positive.',
            'trade_in.valeur_reprise.lte' => 'La valeur de reprise ne peut pas dépasser le prix de vente.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Convertir has_trade_in en booléen
        if ($this->has('has_trade_in')) {
            $this->merge([
                'has_trade_in' => filter_var($this->has_trade_in, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        // Ajouter sold_by automatiquement
        $this->merge([
            'sold_by' => Auth::id(),
        ]);

        // Si c'est un troc, calculer le complément si non fourni
        if ($this->sale_type === 'troc' && $this->has('trade_in.valeur_reprise')) {
            $valeurReprise = (float) ($this->input('trade_in.valeur_reprise') ?? 0);
            $prixVente = (float) ($this->prix_vente ?? 0);

            $this->merge([
                'trade_in' => array_merge($this->input('trade_in', []), [
                    'complement_especes' => $prixVente - $valeurReprise,
                ]),
            ]);
        }

        // Pour vente directe, définir payment_status à paid par défaut
        if ($this->buyer_type === 'direct') {
            $this->merge([
                'payment_status' => 'paid',
                'amount_paid' => $this->prix_vente,
                'payment_method' => $this->payment_method ?? 'cash',
            ]);
        }

        // Pour revendeur sans paiement initial
        if ($this->buyer_type === 'reseller' && $this->payment_status === 'unpaid') {
            $this->merge([
                'amount_paid' => 0,
            ]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Vérifier que le produit est disponible
            if ($this->product_id) {
                $product = \App\Models\Product::find($this->product_id);
                if ($product && ! $product->isAvailable()) {
                    $validator->errors()->add(
                        'product_id',
                        'Ce produit n\'est plus disponible à la vente.'
                    );
                }
            }

            // Si troc, vérifier que tous les champs sont présents
            if ($this->sale_type === 'troc') {
                if (
                    ! $this->has('trade_in.modele_recu') ||
                    ! $this->has('trade_in.imei_recu') ||
                    ! $this->has('trade_in.valeur_reprise')
                ) {
                    $validator->errors()->add(
                        'trade_in',
                        'Les informations de reprise sont incomplètes pour un troc.'
                    );
                }
            }

            // Vérifier la cohérence du montant payé
            if ($this->buyer_type === 'reseller' && $this->payment_status === 'partial') {
                if (! $this->has('amount_paid') || $this->amount_paid <= 0) {
                    $validator->errors()->add(
                        'amount_paid',
                        'Le montant payé est requis pour un paiement partiel.'
                    );
                }
            }
        });
    }
}
