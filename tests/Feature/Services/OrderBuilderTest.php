<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\DTOs\Ingredient;
use App\DTOs\Order;
use App\DTOs\Product;
use App\Models\Ingredient as IngredientModel;
use App\Services\OrderBuilder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(OrderBuilder::class)]
class OrderBuilderTest extends TestCase
{
    public function test_instantiation_creates_new_empty_order_instance(): void
    {
        $builder = new OrderBuilder();
        $order = $builder->get();

        static::assertEquals(new Order(), $order);
    }

    public function test_creating_new_order_instance(): void
    {
        $order = new Order();
        $builder = new OrderBuilder();

        $builder->usingOrder($order);
        $newOrder = $builder->newOrder()->get();

        static::assertNotSame($order, $newOrder);
    }

    public function test_using_order_instance(): void
    {
        $order = new Order();
        $builder = new OrderBuilder();

        $builder->usingOrder($order);
        $newOrder = $builder->get();

        static::assertSame($order, $newOrder);
    }

    public function test_adding_products(): void
    {
        $builder = new OrderBuilder();
        $builder->addProduct($product = new Product(1, 2));

        $order = $builder->get();

        static::assertEquals([$product], $order->products()->values()->all());
    }

    public function test_adding_ingredient_models(): void
    {
        $builder = new OrderBuilder();
        $builder->addProduct($product = new Product(1, $quantity = 2));
        $pivot = new Pivot(['amount' => $amount = 10]);
        $ingredient = IngredientModel::factory()->makeOne()->forceFill(['id' => $id = 1]);
        $ingredient->setRelation('pivot', $pivot);

        $builder->addIngredientModels([$ingredient, $ingredient], $product->id);

        $order = $builder->get();

        $expected = [new Ingredient($id, $ingredient->stock, $ingredient->threshold, $quantity * $amount * 2)];

        static::assertEquals($expected, $order->ingredients()->values()->all());
    }
}
