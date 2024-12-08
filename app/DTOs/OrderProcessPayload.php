<?php

declare(strict_types=1);

namespace App\DTOs;

use App\DTOs\Order as OrderDTO;
use App\Models\Order as OrderModel;

class OrderProcessPayload
{
    /** Constructor. */
    public function __construct(
        public OrderModel $model,
        public OrderDTO $dto
    ) {
    }
}
