<?php

namespace App\Livewire;

use App\Models\Product;
use App\Services\SaleService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class QuickSell extends Component
{
    public Product $product;

    // Type de vente
    public $sale_type = 'achat_direct';

    // Informations client
    public $client_name = '';

    public $client_phone = '';

    // Troc
    public $trade_in_modele_recu = '';

    public $trade_in_imei_recu = '';

    public $trade_in_valeur_reprise = 0;

    public $trade_in_etat_recu = '';

    // Notes
    public $notes = '';

    // Calculé
    public $complement_especes = 0;

    protected $rules = [
        'sale_type' => 'required|in:achat_direct,troc',
        'client_name' => 'nullable|string|max:255',
        'client_phone' => 'nullable|string|max:20',
        'notes' => 'nullable|string',

        // Troc (conditionnels)
        'trade_in_modele_recu' => 'required_if:sale_type,troc|string|max:255',
        'trade_in_imei_recu' => 'required_if:sale_type,troc|string|size:15',
        'trade_in_valeur_reprise' => 'required_if:sale_type,troc|numeric|min:0',
        'trade_in_etat_recu' => 'nullable|string',
    ];

    protected $messages = [
        'trade_in_modele_recu.required_if' => 'Le modèle reçu est requis pour un troc.',
        'trade_in_imei_recu.required_if' => 'L\'IMEI reçu est requis pour un troc.',
        'trade_in_imei_recu.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
        'trade_in_valeur_reprise.required_if' => 'La valeur de reprise est requise pour un troc.',
        'trade_in_valeur_reprise.min' => 'La valeur de reprise doit être positive.',
    ];

    public function mount(Product $product)
    {
        // Vérifier que le produit est disponible
        if (! $product->isAvailable()) {
            session()->flash('error', 'Ce produit n\'est pas disponible à la vente.');

            return redirect()->route('products.show', $product);
        }

        $this->product = $product;
    }

    /**
     * Calcul automatique du complément quand la valeur de reprise change
     */
    public function updatedTradeInValeurReprise($value)
    {
        $this->complement_especes = $this->product->prix_vente - floatval($value);
    }

    /**
     * Réinitialiser les champs troc quand on change de type
     */
    public function updatedSaleType($value)
    {
        if ($value === 'achat_direct') {
            $this->trade_in_modele_recu = '';
            $this->trade_in_imei_recu = '';
            $this->trade_in_valeur_reprise = 0;
            $this->trade_in_etat_recu = '';
            $this->complement_especes = 0;
        }
    }

    public function submit()
    {
        // Validation
        $this->validate();

        // Validation supplémentaire pour le troc
        if ($this->sale_type === 'troc') {
            if ($this->trade_in_valeur_reprise > $this->product->prix_vente) {
                $this->addError(
                    'trade_in_valeur_reprise',
                    'La valeur de reprise ne peut pas dépasser le prix de vente.'
                );

                return;
            }
        }

        try {
            // Préparer les données
            $data = [
                'product_id' => $this->product->id,
                'sale_type' => $this->sale_type,
                'prix_vente' => $this->product->prix_vente,
                'prix_achat_produit' => $this->product->prix_achat,
                'client_name' => $this->client_name,
                'client_phone' => $this->client_phone,
                'date_vente_effective' => now()->format('Y-m-d'),
                'is_confirmed' => true,
                'sold_by' => Auth::id(),
                'notes' => $this->notes,
            ];

            // Ajouter les données de troc si nécessaire
            if ($this->sale_type === 'troc') {
                $data['has_trade_in'] = true;
                $data['trade_in'] = [
                    'modele_recu' => $this->trade_in_modele_recu,
                    'imei_recu' => $this->trade_in_imei_recu,
                    'valeur_reprise' => $this->trade_in_valeur_reprise,
                    'complement_especes' => $this->complement_especes,
                    'etat_recu' => $this->trade_in_etat_recu,
                ];
            }

            // Créer la vente via le service
            $saleService = app(SaleService::class);
            $sale = $saleService->createSale($data);

            if ($this->sale_type === 'troc') {
                $this->dispatch('trade-in-created');
            }

            session()->flash('success', 'Vente enregistrée avec succès.');

            return redirect()->route('sales.show', $sale);
        } catch (\Exception $e) {
            logger()->error('Erreur lors de la création de la vente', [
                'error' => $e->getMessage(),
                'product_id' => $this->product->id,
            ]);

            session()->flash('error', 'Erreur lors de l\'enregistrement : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.quick-sell');
    }
}
