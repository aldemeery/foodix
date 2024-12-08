<?php

declare(strict_types=1);

namespace Tests\Feature\Pipeline\Order;

use App\DTOs\Ingredient;
use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\Events\IngredientThresholdBreached;
use App\Models\Order;
use App\Pipeline\Order\HandleLowStock;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(HandleLowStock::class)]
class HandleLowStockTest extends TestCase
{
    public function test_handling(): void
    {
        Event::fake();

        $payload = $this->createPayload([
            $breachingIngredient = new Ingredient(id: 1, stock: 30, threshold: 25, required: 10),
            new Ingredient(id: 2, stock: 30, threshold: 25, required: 5),
        ]);
        $step = new HandleLowStock();
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $step->handle($payload, $next);

        Event::assertDispatched(
            IngredientThresholdBreached::class,
            fn (IngredientThresholdBreached $event): bool => $event->ingredientId === $breachingIngredient->id,
        );

        Event::assertDispatched(
            IngredientThresholdBreached::class,
            1,
        );
    }

    /** @param array<int, Ingredient> $ingredients */
    private function createPayload(array $ingredients): OrderProcessPayload
    {
        $dto = new OrderDTO();

        foreach ($ingredients as $ingredient) {
            $dto->addIngredient($ingredient);
        }

        return new OrderProcessPayload(
            Order::factory()->makeOne(),
            $dto,
        );
    }
}
