<?php

declare(strict_types=1);

namespace Tests\Feature\Pipeline\Order;

use App\DTOs\Ingredient as IngredientDTO;
use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\Models\Order;
use App\Pipeline\Order\ValidateOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ValidateOrder::class)]
class ValidateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_handling(): void
    {
        $payload = $this->createPayload([new IngredientDTO(id: 1, stock: 20, threshold: 10, required: 30)]);
        $step = new ValidateOrder();
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        static::expectException(ValidationException::class);
        static::expectExceptionMessage('The order cannot be processed due to insufficient ingredients.');

        $step->handle($payload, $next);
    }

    public function test_handling_with_sufficient_ingredients(): void
    {
        $payload = $this->createPayload([new IngredientDTO(id: 1, stock: 20, threshold: 10, required: 5)]);
        $step = new ValidateOrder();
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $result = $step->handle($payload, $next);

        static::assertSame($payload, $result);
    }

    /** @param array<int, IngredientDTO> $ingredients */
    private function createPayload(array $ingredients): OrderProcessPayload
    {
        $dto = new OrderDTO();

        foreach ($ingredients as $ingredient) {
            $dto->addIngredient($ingredient);
        }

        return new OrderProcessPayload(
            new Order(),
            $dto,
        );
    }
}
