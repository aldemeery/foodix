<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Product::class)]
class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_many_ingredients(): void
    {
        $product = Product::factory()->createOne();
        $ingredients = Ingredient::factory()->count($ingredientsCount = 3)->create();
        $product->ingredients()->attach($ingredients, ['amount' => $amount = 100]);
        $pivot = $product->ingredients()->first()?->getRelationValue('pivot');

        static::assertCount($ingredientsCount, $product->ingredients()->get());
        static::assertInstanceOf(Ingredient::class, $product->ingredients()->first());
        static::assertInstanceOf(Pivot::class, $pivot);
        static::assertSame($amount, $pivot->getAttributeValue('amount'));
    }

    public function test_product_belongs_to_many_orders(): void
    {
        $product = Product::factory()->createOne();
        $orders = Order::factory()->count($ordersCount = 3)->create();
        $product->orders()->attach($orders, ['quantity' => $quantity = 2]);
        $pivot = $product->orders()->first()?->getRelationValue('pivot');

        static::assertCount($ordersCount, $product->orders()->get());
        static::assertInstanceOf(Order::class, $product->orders()->first());
        static::assertInstanceOf(Pivot::class, $pivot);
        static::assertSame($quantity, $pivot->getAttributeValue('quantity'));
    }
}
