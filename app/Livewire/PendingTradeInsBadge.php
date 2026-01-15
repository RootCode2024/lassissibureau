<?php

namespace App\Livewire;

use App\Models\TradeIn;
use Livewire\Component;
use Livewire\Attributes\On;

class PendingTradeInsBadge extends Component
{
    public $count = 0;

    public function mount()
    {
        $this->updateCount();
    }

    #[On('trade-in-created')]
    #[On('trade-in-processed')]
    public function updateCount()
    {
        $this->count = TradeIn::pending()->count();
    }

    public function render()
    {
        return view('livewire.pending-trade-ins-badge');
    }
}
