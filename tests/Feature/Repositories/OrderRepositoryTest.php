<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(OrderRepository::class)]
class OrderRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_persisting_orders(): void
    {
        $product1 = Product::factory()->createOne();
        $product2 = Product::factory()->createOne();

        $repository = $this->createRepository();

        $repository->persist(new Order(), [
            $product1->id => ['quantity' => 2],
            $product2->id => ['quantity' => 3],
        ]);

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('order_product', ['product_id' => $product1->id, 'quantity' => 2]);
        $this->assertDatabaseHas('order_product', ['product_id' => $product2->id, 'quantity' => 3]);
    }

    private function createRepository(): OrderRepository
    {
        return new OrderRepository();
    }
}
