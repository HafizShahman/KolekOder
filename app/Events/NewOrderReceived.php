<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->load(['customer', 'items.product']);
    }

    /**
     * Broadcast on private-shop.{shopId} so only the authenticated
     * shop owner receives this event.
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel("shop.{$this->order->shop_id}")];
    }

    public function broadcastAs(): string
    {
        return 'NewOrderReceived';
    }

    public function broadcastWith(): array
    {
        $orderArray = $this->order->toArray();
        
        // Remove large base64 strings to prevent "Payload too large" errors from Pusher/Reverb
        
        // Remove the shop relation entirely if loaded (might contain shop_logo base64)
        if (isset($orderArray['shop'])) {
            unset($orderArray['shop']);
        }
        
        if (isset($orderArray['items'])) {
            foreach ($orderArray['items'] as &$item) {
                if (isset($item['product']['image'])) {
                    unset($item['product']['image']);
                }
            }
        }
        
        return ['order' => $orderArray];
    }
}
