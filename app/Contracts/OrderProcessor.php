<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\Order as OrderDTO;
use App\Models\Order as OrderModel;

interface OrderProcessor
{
    /**
     * Process the given order data transfer object (DTO) and generate an order model.
     * This involves applying necessary transformations, validations, and operations
     * to turn the input order data into a persisted or ready-to-use order instance.
     */
    public function process(OrderDTO $order): OrderModel;
}
