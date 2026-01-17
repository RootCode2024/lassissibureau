<?php

namespace App\Livewire\Sales;

use App\Events\PaymentRecorded;
use App\Models\Sale;
use App\Services\SaleService;
use Livewire\Attributes\On;
use Livewire\Component;

class PaymentManager extends Component
{
    public Sale $sale;

    public $showModal = false;

    // Champs du formulaire
    public $amount = null;

    public $payment_method = 'cash';

    public $payment_date;

    public $notes = null;

    public function mount(Sale $sale)
    {
        $this->sale = $sale;
        $this->payment_date = now()->format('Y-m-d');
    }

    public function openModal()
    {
        // Réinitialiser le formulaire
        $this->reset(['amount', 'notes']);
        $this->payment_method = 'cash';
        $this->payment_date = now()->format('Y-m-d');

        // Pré-remplir avec le solde restant
        $this->amount = $this->sale->amount_remaining;

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['amount', 'payment_method', 'notes']);
    }

    public function recordPayment(SaleService $saleService)
    {
        // Validation
        $validated = $this->validate([
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:'.$this->sale->amount_remaining,
            ],
            'payment_method' => 'required|in:cash,mobile_money,bank_transfer,check',
            'payment_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ], [
            'amount.required' => 'Le montant est requis.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'amount.max' => 'Le montant ne peut pas dépasser le solde restant ('.number_format($this->sale->amount_remaining, 0, ',', ' ').' FCFA).',
            'payment_method.required' => 'La méthode de paiement est requise.',
            'payment_date.required' => 'La date du paiement est requise.',
            'payment_date.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ]);

        try {
            // Enregistrer le paiement
            $payment = $saleService->recordPayment($this->sale, $this->amount, [
                'payment_method' => $this->payment_method,
                'payment_date' => $this->payment_date,
                'notes' => $this->notes,
            ]);

            // Rafraîchir la vente
            $this->sale->refresh();

            // Émettre l'événement
            event(new PaymentRecorded($payment));

            // Notification de succès
            $this->dispatch('payment-recorded', [
                'message' => 'Paiement de '.number_format($this->amount, 0, ',', ' ').' FCFA enregistré avec succès.',
            ]);

            session()->flash('success', 'Paiement enregistré avec succès.');

            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur : '.$e->getMessage());
        }
    }

    #[On('sale-updated')]
    public function refreshSale()
    {
        $this->sale->refresh();
    }

    public function render()
    {
        return view('livewire.sales.payment-manager', [
            'payments' => $this->sale->payments()->with('recorder')->get(),
        ]);
    }
}
