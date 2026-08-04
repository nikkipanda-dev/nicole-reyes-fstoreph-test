<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderController
{
    /**
     * Orders index
     *
     * @return \Inertia\Response
     */
    public function index(): Response
    {
        $orders = Order::with('product:id,product_name')
            ->get(['id', 'product_id', 'price'])
            ->map(fn (Order $order) => [
                'id' => $order->id,
                'product_name' => $order->product->product_name,
                'price' => $order->price,
            ]);

        return Inertia::render('Order/Index', [
            'orders' => $orders,
        ]);
    }
}
