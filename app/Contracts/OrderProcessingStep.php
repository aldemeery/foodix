<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\OrderProcessPayload;
use Closure;

interface OrderProcessingStep
{
    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload;
}
