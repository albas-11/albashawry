<?php

namespace App\Notifications\Admin;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPaidNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Payment Received',
            'message' => "Payment of {$this->order->total} MAD received for order #{$this->order->order_number}",
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'type' => 'payment_received',
            'url' => route('admin.orders.show', $this->order->id),
            'icon' => 'credit-card',
            'color' => 'primary'
        ];
    }
}
