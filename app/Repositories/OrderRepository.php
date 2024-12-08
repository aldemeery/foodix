<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\OrderRepository as OrderRepositoryContract;
use App\Models\Order;

class OrderRepository implements OrderRepositoryContract
{
    /**
     * Persist the given order and its associated products into the database.
     *
     * @param array<int, array<string, int>> $products
     */
    public function persist(Order $order, array $products): Order
    {
        $order->save();

        $order->products()->attach($products);

        return $order->load('products');
    }
}
