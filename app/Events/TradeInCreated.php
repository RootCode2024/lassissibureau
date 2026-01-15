<?php

namespace App\Events;

use App\Models\TradeIn;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TradeInCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public TradeIn $tradeIn)
    {
    }
}
