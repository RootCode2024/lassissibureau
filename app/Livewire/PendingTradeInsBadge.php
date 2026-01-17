<?php

namespace App\Livewire;

use App\Models\TradeIn;
use Livewire\Attributes\On;
use Livewire\Component;

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
