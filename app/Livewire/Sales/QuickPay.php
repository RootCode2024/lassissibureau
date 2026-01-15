<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use Livewire\Component;
use App\Services\SaleService;

class QuickPay extends Component
{
    public $saleId;
    public $amount;
    public $paymentMethod = 'cash';
    public $notes;

    public function mount($saleId)
    {
        $this->saleId = $saleId;
        $sale = Sale::find($saleId);
        if ($sale) {
            $this->amount = $sale->amount_remaining;
        }
    }

    public function save(SaleService $saleService)
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
            'paymentMethod' => 'required|string',
            'notes' => 'nullable|string|max:255',
        ]);

        $sale = Sale::findOrFail($this->saleId);

        if ($this->amount > $sale->amount_remaining) {
            $this->addError('amount', 'Le montant ne peut pas dépasser le reste à payer (' . number_format($sale->amount_remaining, 0, ',', ' ') . ' FCFA).');
            return;
        }

        $saleService->recordPayment($sale, $this->amount, [
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
        ]);

        // Émettre un événement pour rafraîchir la liste parente
        $this->dispatch('payment-recorded');
    }

    public function render()
    {
        return view('livewire.sales.quick-pay');
    }
}
