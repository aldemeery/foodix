<?php

declare(strict_types=1);

namespace Tests\Feature\Pipeline\Order;

use App\Contracts\IngredientRepository;
use App\DTOs\Ingredient as IngredientDTO;
use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\Models\Ingredient;
use App\Models\Order;
use App\Pipeline\Order\UpdateStock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UpdateStock::class)]
class UpdateStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_handling(): void
    {
        $payload = $this->createPayload(Ingredient::factory()->count(2)->create(['stock' => 50])->all());
        $step = new UpdateStock($this->app->make(IngredientRepository::class));
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $step->handle($payload, $next);

        foreach ($payload->dto->ingredients() as $ingredient) {
            $this->assertDatabaseHas('ingredients', [
                'id' => $ingredient->id,
                'stock' => $ingredient->stock - $ingredient->required,
            ]);
        }
    }

    public function test_update_happens_in_single_query(): void
    {
        $payload = $this->createPayload(Ingredient::factory()->count(2)->create(['stock' => 50])->all());
        $step = new UpdateStock($this->app->make(IngredientRepository::class));
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $this->expectsDatabaseQueryCount(1);

        $step->handle($payload, $next);
    }

    /** @param array<int, Ingredient> $ingredients */
    private function createPayload(array $ingredients): OrderProcessPayload
    {
        $dto = new OrderDTO();

        foreach ($ingredients as $ingredient) {
            $dto->addIngredient(new IngredientDTO(
                id: $ingredient->id,
                stock: $ingredient->stock,
                threshold: $ingredient->threshold,
                required: 30,
            ));
        }

        return new OrderProcessPayload(
            new Order(),
            $dto,
        );
    }
}
