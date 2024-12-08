<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Order;

interface OrderRepository
{
    /**
     * Persist the given order and its associated products into the database.
     *
     * @param array<int, array<string, int>> $products
     */
    public function persist(Order $order, array $products): Order;
}
