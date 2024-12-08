<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Order::class)]
class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_many_products(): void
    {
        $order = Order::factory()->createOne();
        $products = Product::factory()->count($productsCount = 3)->create();
        $order->products()->attach($products, ['quantity' => $quantity = 2]);
        $pivot = $order->products()->first()?->getRelationValue('pivot');

        static::assertCount($productsCount, $order->products()->get());
        static::assertInstanceOf(Product::class, $order->products()->first());
        static::assertInstanceOf(Pivot::class, $pivot);
        static::assertSame($quantity, $pivot->getAttributeValue('quantity'));
    }
}
