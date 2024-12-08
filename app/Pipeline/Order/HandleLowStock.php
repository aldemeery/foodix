<?php

declare(strict_types=1);

namespace App\Pipeline\Order;

use App\Contracts\OrderProcessingStep;
use App\DTOs\Ingredient;
use App\DTOs\OrderProcessPayload;
use App\Events\IngredientThresholdBreached;
use Closure;

class HandleLowStock implements OrderProcessingStep
{
    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload
    {
        $payload->dto->ingredients()->each(function (Ingredient $ingredient): void {
            IngredientThresholdBreached::dispatchIf(
                $ingredient->breachesThreshold(),
                $ingredient->id,
            );
        });

        return $next($payload);
    }
}
