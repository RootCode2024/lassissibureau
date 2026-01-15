<?php

namespace App\Livewire\Sales;

use App\Models\Sale;
use App\Services\SaleService;
use App\Events\SaleConfirmed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class ResellerSales extends Component
{
    use WithPagination;

    #[Url]
    public $reseller_id = '';

    #[Url]
    public $status = 'pending';

    public $showConfirmModal = false;
    public $showReturnModal = false;
    public $selectedSale = null;
    public $confirmNotes = '';
    public $returnReason = '';
    public $confirmPaymentAmount = 0;
    public $confirmPaymentMethod = 'cash';

    public function updatedResellerId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function openConfirmModal(Sale $sale)
    {
        $this->selectedSale = $sale;
        $this->confirmNotes = '';
        $this->confirmPaymentAmount = $sale->amount_remaining;
        $this->confirmPaymentMethod = 'cash';
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->selectedSale = null;
        $this->reset(['confirmNotes', 'confirmPaymentAmount', 'confirmPaymentMethod']);
    }

    public function confirmSale(SaleService $saleService)
    {
        if (!$this->selectedSale) {
            return;
        }

        $this->validate([
            'confirmPaymentAmount' => 'nullable|numeric|min:0|max:' . $this->selectedSale->amount_remaining,
            'confirmPaymentMethod' => 'required|in:cash,mobile_money,bank_transfer,check',
            'confirmNotes' => 'nullable|string|max:500',
        ]);

        try {
            $data = [
                'notes' => $this->confirmNotes,
            ];

            if ($this->confirmPaymentAmount > 0) {
                $data['payment_amount'] = $this->confirmPaymentAmount;
                $data['payment_method'] = $this->confirmPaymentMethod;
            }

            $sale = $saleService->confirmResellerSale($this->selectedSale, $data);

            event(new SaleConfirmed($sale));

            session()->flash('success', 'Vente confirmée avec succès.');

            $this->closeConfirmModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function openReturnModal(Sale $sale)
    {
        $this->selectedSale = $sale;
        $this->returnReason = '';
        $this->showReturnModal = true;
    }

    public function closeReturnModal()
    {
        $this->showReturnModal = false;
        $this->selectedSale = null;
        $this->returnReason = '';
    }

    public function returnProduct(SaleService $saleService)
    {
        if (!$this->selectedSale) {
            return;
        }

        $this->validate([
            'returnReason' => 'required|string|min:10|max:500',
        ], [
            'returnReason.required' => 'Le motif du retour est requis.',
            'returnReason.min' => 'Le motif doit contenir au moins 10 caractères.',
        ]);

        try {
            $saleService->returnFromReseller($this->selectedSale, $this->returnReason);

            session()->flash('success', 'Produit retourné en stock avec succès.');

            $this->closeReturnModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur : ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = Sale::with(['product.productModel', 'reseller', 'seller', 'payments'])
            ->whereNotNull('reseller_id');

        // Filtre par revendeur
        if ($this->reseller_id) {
            $query->where('reseller_id', $this->reseller_id);
        }

        // Filtre par statut
        if ($this->status === 'pending') {
            $query->where('is_confirmed', false);
        } elseif ($this->status === 'confirmed') {
            $query->where('is_confirmed', true);
        }

        $sales = $query->latest('date_depot_revendeur')->paginate(15);

        // Statistiques
        $stats = [
            'pending_count' => Sale::pending()->count(),
            'confirmed_count' => Sale::confirmed()->whereNotNull('reseller_id')->count(),
            'pending_value' => Sale::pending()->sum('prix_vente'),
        ];

        // Revendeurs pour le filtre
        $resellers = \App\Models\Reseller::whereHas('sales')->orderBy('name')->get();

        // return view('livewire.sales.reseller-sales', [
        //     'sales' => $sales,
        //     'stats' => $stats,
        //     'resellers' => $resellers,
        // ]);

        return view('livewire.sales.reseller-sales',
        [
            'sales' => $sales,
            'stats' => $stats,
            'resellers' => $resellers,
        ])
        ->title('Vente revendeurs')
        ->layout('layouts.app', [
            'header' => 'Ventes revendeurs'
        ]);
    }
}
