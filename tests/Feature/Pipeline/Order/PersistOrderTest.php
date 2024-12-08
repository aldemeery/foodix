<?php

declare(strict_types=1);

namespace Tests\Feature\Pipeline\Order;

use App\Contracts\OrderRepository;
use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\DTOs\Product as ProductDTO;
use App\Models\Order;
use App\Models\Product;
use App\Pipeline\Order\PersistOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(PersistOrder::class)]
class PersistOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_handling(): void
    {
        $payload = $this->createPayload(Product::factory()->count(2)->create()->all());
        $step = new PersistOrder($this->app->make(OrderRepository::class));
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $step->handle($payload, $next);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', ['id' => 1]);
        $this->assertDatabaseCount('order_product', 2);

        foreach ($payload->dto->products() as $product) {
            $this->assertDatabaseHas('order_product', [
                'order_id' => 1,
                'product_id' => $product->id,
                'quantity' => $product->quantity,
            ]);
        }
    }

    /** @param array<int, Product> $products */
    private function createPayload(array $products): OrderProcessPayload
    {
        $dto = new OrderDTO();

        foreach ($products as $product) {
            $dto->addProduct(new ProductDTO(
                id: $product->id,
                quantity: 2,
            ));
        }

        return new OrderProcessPayload(
            new Order(),
            $dto,
        );
    }
}
