<?php

namespace App\Livewire;

use App\Models\ProductModel;
use App\Models\TradeIn;
use App\Services\SaleService;
use Livewire\Component;

class CreateTradeInProduct extends Component
{
    public TradeIn $tradeIn;

    public $product_model_id = '';

    public $prix_vente = '';

    public $marge_percentage = 20;

    public $notes = '';

    // Flag pour éviter les boucles infinies lors des mises à jour
    private $isUpdating = false;

    protected $rules = [
        'product_model_id' => 'required|exists:product_models,id',
        'prix_vente' => 'required|numeric|min:0',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'product_model_id.required' => 'Le modèle est requis.',
        'prix_vente.required' => 'Le prix de vente est requis.',
    ];

    public function mount(TradeIn $tradeIn)
    {
        $this->tradeIn = $tradeIn;

        // Calculer le prix de vente suggéré (marge 20%)
        $this->prix_vente = round($tradeIn->valeur_reprise * 1.2, 2);

        // Pré-remplir les notes
        $this->notes = 'Reçu en troc - Vente #'.$tradeIn->sale_id;
    }

    /**
     * Recalculer le prix de vente quand la marge change
     */
    public function updatedMargePercentage($value)
    {
        // Éviter les boucles infinies
        if ($this->isUpdating) {
            return;
        }

        $this->isUpdating = true;

        try {
            if ($value !== '' && $value !== null && is_numeric($value)) {
                $marge = floatval($value);

                // Calculer le nouveau prix de vente basé sur la marge
                $this->prix_vente = round($this->tradeIn->valeur_reprise * (1 + $marge / 100), 2);
            }
        } finally {
            $this->isUpdating = false;
        }
    }

    /**
     * Recalculer la marge quand le prix de vente change
     */
    public function updatedPrixVente($value)
    {
        // Éviter les boucles infinies
        if ($this->isUpdating) {
            return;
        }

        $this->isUpdating = true;

        try {
            if ($value !== '' && $value !== null && is_numeric($value)) {
                $prixVente = floatval($value);
                $valeurReprise = $this->tradeIn->valeur_reprise;

                // Éviter la division par zéro
                if ($valeurReprise > 0) {
                    // Calculer la nouvelle marge en pourcentage
                    $this->marge_percentage = round((($prixVente - $valeurReprise) / $valeurReprise) * 100, 2);
                } else {
                    $this->marge_percentage = 0;
                }
            }
        } finally {
            $this->isUpdating = false;
        }
    }

    public function submit()
    {
        $this->validate();

        try {
            $saleService = app(SaleService::class);

            $product = $saleService->createTradeInProduct(
                $this->tradeIn,
                $this->product_model_id,
                $this->prix_vente,
                $this->notes
            );

            session()->flash('success', 'Produit reçu en troc créé avec succès.');

            return redirect()->route('products.show', $product);
        } catch (\Exception $e) {
            logger()->error('Erreur création produit troc', [
                'error' => $e->getMessage(),
                'trade_in_id' => $this->tradeIn->id,
            ]);

            session()->flash('error', 'Erreur : '.$e->getMessage());
        }
    }

    public function render()
    {
        $productModels = ProductModel::orderBy('brand')->orderBy('name')->get();

        return view('livewire.create-trade-in-product', [
            'productModels' => $productModels,
        ]);
    }
}
