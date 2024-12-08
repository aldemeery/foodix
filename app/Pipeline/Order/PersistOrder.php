<?php

declare(strict_types=1);

namespace App\Pipeline\Order;

use App\Contracts\OrderProcessingStep;
use App\Contracts\OrderRepository;
use App\DTOs\OrderProcessPayload;
use App\DTOs\Product;
use Closure;
use Psl\Type;

class PersistOrder implements OrderProcessingStep
{
    /** Constructor. */
    public function __construct(
        private OrderRepository $orders,
    ) {
    }

    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload
    {
        $payload->model = $this->orders->persist(
            $payload->model,
            Type\dict(
                Type\int(),
                Type\dict(Type\string(), Type\int()),
            )->assert($payload->dto->products()->mapWithKeys(fn (Product $product): array => [
                $product->id => ['quantity' => $product->quantity],
            ])->toArray()),
        );

        return $next($payload);
    }
}
