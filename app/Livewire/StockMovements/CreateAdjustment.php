<?php

namespace App\Livewire\StockMovements;

use App\Models\Product;
use App\Enums\StockMovementType;
use App\Services\StockService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateAdjustment extends Component
{
    public $product_id = '';
    public $adjustment_type = 'correction'; // 'correction', 'casse', 'vol', 'perte'
    public $quantity = 1;
    public $justification = '';
    public $notes = '';

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'adjustment_type' => 'required|in:correction,casse,vol,perte',
        'quantity' => 'required|integer|min:1',
        'justification' => 'required|string|max:500',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'product_id.required' => 'Le produit est requis.',
        'adjustment_type.required' => 'Le type d\'ajustement est requis.',
        'quantity.required' => 'La quantité est requise.',
        'justification.required' => 'La justification est requise.',
    ];

    public function submit()
    {
        $this->validate();

        try {
            $product = Product::findOrFail($this->product_id);
            $stockService = app(StockService::class);

            // Déterminer le type de mouvement
            $movementType = match ($this->adjustment_type) {
                'correction' => StockMovementType::CORRECTION_MOINS,
                'casse' => StockMovementType::CASSE,
                'vol' => StockMovementType::VOL,
                'perte' => StockMovementType::PERTE,
            };

            // Créer le mouvement
            $movement = $stockService->createMovement([
                'product_id' => $this->product_id,
                'type' => $movementType->value,
                'quantity' => $this->quantity,
                'state_after' => $product->state->value,
                'location_after' => $product->location->value,
                'justification' => $this->justification,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            session()->flash('success', 'Ajustement de stock enregistré avec succès.');
            return redirect()->route('stock-movements.show', $movement);
        } catch (\Exception $e) {
            logger()->error('Erreur lors de l\'ajustement', [
                'error' => $e->getMessage(),
                'product_id' => $this->product_id,
            ]);

            session()->flash('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $products = Product::with('productModel')
            ->inStock()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.stock-movements.create-adjustment', [
            'products' => $products,
        ]);
    }
}
