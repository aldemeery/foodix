<?php

declare(strict_types=1);

namespace App\Pipeline\Order;

use App\Contracts\OrderProcessingStep;
use App\DTOs\OrderProcessPayload;
use App\Events\OrderCreated;
use Closure;

class DispatchOrderCreationEvents implements OrderProcessingStep
{
    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload
    {
        OrderCreated::dispatch($payload->model->id);

        // Dispatch other events here...

        return $next($payload);
    }
}
