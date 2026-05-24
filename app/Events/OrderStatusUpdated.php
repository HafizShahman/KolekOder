<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->load(['items.product']);
    }

    /**
     * Public channel — no auth required. Any browser holding the
     * order ID can subscribe and get live status updates.
     */
    public function broadcastOn(): array
    {
        return [new Channel("orders.{$this->order->id}")];
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return ['order' => $this->order];
    }
}
