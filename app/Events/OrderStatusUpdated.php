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

    /**
     * Only broadcast non-sensitive status fields. This is a public channel,
     * so never expose customer details, notes, or item data here.
     */
    public function broadcastWith(): array
    {
        return [
            'order' => [
                'id'           => $this->order->id,
                'order_number' => $this->order->order_number,
                'status'       => $this->order->status,
                'updated_at'   => $this->order->updated_at,
            ],
        ];
    }
}
