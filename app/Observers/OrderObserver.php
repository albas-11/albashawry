<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use App\Notifications\Admin\NewOrderNotification;

class OrderObserver
{
    /**
     * Quand une commande est créée
     */
    public function created(Order $order): void
    {
        // Charger l'utilisateur
        $order->load('user');


        foreach (\App\Models\User::admins() as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }
    }
}
