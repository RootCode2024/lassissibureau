<?php

namespace App\Livewire\StockMovements;

use App\Enums\ProductLocation;
use App\Enums\ProductState;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\ProductModel;
use App\Services\StockService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateReception extends Component
{
    // Mode: nouveau produit ou réception d'un produit existant
    public $mode = 'new'; // 'new' ou 'existing'

    // Pour nouveau produit
    public $product_model_id = '';

    public $imei = '';

    public $serial_number = '';

    public $prix_achat = '';

    public $prix_vente = '';

    public $fournisseur = '';

    public $date_achat = '';

    // Pour produit existant
    public $existing_product_id = '';

    // Commun
    public $notes = '';

    public $condition = '';

    public $defauts = '';

    // Pour calcul automatique prix vente
    public $marge_percentage = 20;

    protected function rules()
    {
        $rules = [
            'mode' => 'required|in:new,existing',
            'notes' => 'nullable|string',
            'condition' => 'nullable|string',
            'defauts' => 'nullable|string',
        ];

        if ($this->mode === 'new') {
            $rules = array_merge($rules, [
                'product_model_id' => 'required|exists:product_models,id',
                'imei' => 'required|string|size:15|unique:products,imei',
                'serial_number' => 'nullable|string|max:255',
                'prix_achat' => 'required|numeric|min:0',
                'prix_vente' => 'required|numeric|min:0|gte:prix_achat',
                'fournisseur' => 'nullable|string|max:255',
                'date_achat' => 'required|date',
                'marge_percentage' => 'nullable|numeric|min:0|max:100',
            ]);
        } else {
            $rules['existing_product_id'] = 'required|exists:products,id';
        }

        return $rules;
    }

    protected $messages = [
        'product_model_id.required' => 'Le modèle est requis.',
        'imei.required' => 'L\'IMEI est requis.',
        'imei.size' => 'L\'IMEI doit contenir exactement 15 chiffres.',
        'imei.unique' => 'Cet IMEI existe déjà dans le système.',
        'prix_achat.required' => 'Le prix d\'achat est requis.',
        'prix_vente.gte' => 'Le prix de vente doit être supérieur ou égal au prix d\'achat.',
        'date_achat.required' => 'La date d\'achat est requise.',
    ];

    public function mount()
    {
        $this->date_achat = now()->format('Y-m-d');
    }

    /**
     * Calcul automatique du prix de vente basé sur la marge
     */
    public function updatedPrixAchat($value)
    {
        if ($value && $this->marge_percentage) {
            $this->prix_vente = round($value * (1 + $this->marge_percentage / 100), 2);
        }
    }

    public function updatedMargePercentage($value)
    {
        if ($this->prix_achat && $value) {
            $this->prix_vente = round($this->prix_achat * (1 + $value / 100), 2);
        }
    }

    public function submit()
    {
        $this->validate();

        try {
            $stockService = app(StockService::class);

            if ($this->mode === 'new') {
                // Créer le nouveau produit
                $product = Product::create([
                    'product_model_id' => $this->product_model_id,
                    'imei' => $this->imei,
                    'serial_number' => $this->serial_number,
                    'state' => ProductState::DISPONIBLE,
                    'location' => ProductLocation::BOUTIQUE,
                    'prix_achat' => $this->prix_achat,
                    'prix_vente' => $this->prix_vente,
                    'date_achat' => $this->date_achat,
                    'fournisseur' => $this->fournisseur,
                    'notes' => $this->notes,
                    'condition' => $this->condition,
                    'defauts' => $this->defauts,
                    'created_by' => Auth::id(),
                ]);

                // Créer le mouvement de stock
                $stockService->createMovement([
                    'product_id' => $product->id,
                    'type' => StockMovementType::RECEPTION_FOURNISSEUR->value,
                    'quantity' => 1,
                    'state_after' => ProductState::DISPONIBLE->value,
                    'location_after' => ProductLocation::BOUTIQUE->value,
                    'notes' => 'Réception fournisseur'.($this->fournisseur ? " - {$this->fournisseur}" : ''),
                    'user_id' => Auth::id(),
                ]);

                session()->flash('success', 'Produit reçu et ajouté au stock avec succès.');
            } else {
                // Réception d'un produit existant (retour fournisseur, etc.)
                $product = Product::findOrFail($this->existing_product_id);

                $stockService->createMovement([
                    'product_id' => $product->id,
                    'type' => StockMovementType::RECEPTION_FOURNISSEUR->value,
                    'quantity' => 1,
                    'state_after' => ProductState::DISPONIBLE->value,
                    'location_after' => ProductLocation::BOUTIQUE->value,
                    'notes' => $this->notes,
                    'user_id' => Auth::id(),
                ]);

                session()->flash('success', 'Réception enregistrée avec succès.');
            }

            return redirect()->route('stock-movements.show', $product->lastMovement);
        } catch (\Exception $e) {
            logger()->error('Erreur lors de la réception', [
                'error' => $e->getMessage(),
                'mode' => $this->mode,
            ]);

            session()->flash('error', 'Erreur lors de l\'enregistrement : '.$e->getMessage());
        }
    }

    public function render()
    {
        $productModels = ProductModel::orderBy('brand')->orderBy('name')->get();
        $existingProducts = Product::with('productModel')
            ->where('location', ProductLocation::FOURNISSEUR)
            ->get();

        return view('livewire.stock-movements.create-reception', [
            'productModels' => $productModels,
            'existingProducts' => $existingProducts,
        ]);
    }
}
