<?php

namespace App\Livewire\Sales;

use App\Enums\PaymentStatus;
use App\Models\Sale;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PendingPayments extends Component
{
    use WithPagination;

    #[Url]
    public $reseller_id = '';

    #[Url]
    public $status_filter = 'all';

    #[Url]
    public $sort_by = 'due_date';

    #[Url]
    public $sort_direction = 'asc';

    public $selectedSaleId = null;

    public $showQuickPayModal = false;

    public $quickPayAmount = 0;

    public function mount()
    {
        //
    }

    public function updatedResellerId()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sort_by === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $field;
            $this->sort_direction = 'asc';
        }
    }

    public function openQuickPay($saleId)
    {
        $sale = Sale::find($saleId);

        if ($sale) {
            $this->selectedSaleId = $saleId;
            $this->quickPayAmount = $sale->amount_remaining;
            $this->showQuickPayModal = true;
        }
    }

    public function closeQuickPayModal()
    {
        $this->showQuickPayModal = false;
        $this->selectedSaleId = null;
        $this->quickPayAmount = 0;
    }

    #[On('payment-recorded')]
    public function refreshList()
    {
        // Rafraîchir la liste après un paiement et fermer la modale
        $this->closeQuickPayModal();
    }

    public function render()
    {
        $query = Sale::with(['product.productModel', 'reseller', 'seller', 'payments'])
            ->whereIn('payment_status', [PaymentStatus::UNPAID, PaymentStatus::PARTIAL]);

        // Filtre par revendeur
        if ($this->reseller_id) {
            $query->where('reseller_id', $this->reseller_id);
        }

        // Filtre par statut
        if ($this->status_filter !== 'all') {
            $query->where('payment_status', $this->status_filter);
        }

        // Tri
        switch ($this->sort_by) {
            case 'due_date':
                $query->orderBy('payment_due_date', $this->sort_direction);
                break;
            case 'amount':
                $query->orderBy('amount_remaining', $this->sort_direction);
                break;
            case 'reseller':
                $query->join('resellers', 'sales.reseller_id', '=', 'resellers.id')
                    ->orderBy('resellers.name', $this->sort_direction)
                    ->select('sales.*');
                break;
            default:
                $query->orderBy('payment_due_date', 'asc');
        }

        $sales = $query->paginate(15);

        // Statistiques
        $stats = [
            'total_unpaid' => Sale::unpaid()->sum('amount_remaining'),
            'total_partial' => Sale::partiallyPaid()->sum('amount_remaining'),
            'overdue_count' => Sale::overdue()->count(),
            'overdue_amount' => Sale::overdue()->sum('amount_remaining'),
        ];

        // Revendeurs pour le filtre
        $resellers = \App\Models\Reseller::whereHas('sales', function ($q) {
            $q->withPendingPayment();
        })->orderBy('name')->get();

        return view('livewire.sales.pending-payments', [
            'sales' => $sales,
            'stats' => $stats,
            'resellers' => $resellers,
        ])
            ->title('Paiements en attente')
            ->layout('layouts.app', [
                'header' => 'Paiements en attente',
            ]);
    }
}
