<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\Ingredient;
use App\DTOs\Order;
use App\DTOs\Product;
use OutOfBoundsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Order::class)]
class OrderTest extends TestCase
{
    public function test_getting_products(): void
    {
        $product1 = new Product(1, 2);
        $product2 = new Product(3, 4);

        $order = new Order();
        $order->addProduct($product1);
        $order->addProduct($product2);

        static::assertContainsOnlyInstancesOf(Product::class, $order->products());
        static::assertEquals([$product1, $product2], $order->products()->values()->all());
        static::assertEquals([$product1->id, $product2->id], $order->products()->keys()->all());
    }

    public function test_getting_ingredients(): void
    {
        $ingredient1 = new Ingredient(1, 100, 10, 5);
        $ingredient2 = new Ingredient(2, 200, 20, 10);

        $order = new Order();

        $order->addIngredient($ingredient1);
        $order->addIngredient($ingredient2);

        static::assertContainsOnlyInstancesOf(Ingredient::class, $order->ingredients());
        static::assertEquals([$ingredient1, $ingredient2], $order->ingredients()->values()->all());
        static::assertEquals([$ingredient1->id, $ingredient2->id], $order->ingredients()->keys()->all());
    }

    public function test_adding_products(): void
    {
        $order = new Order();
        $order->addProduct($product = new Product(1, 2));

        static::assertEquals($product, $order->getProduct(1));
    }

    public function test_adding_products_is_additive(): void
    {
        $product1 = new Product(1, 2);

        $order = new Order();

        $order->addProduct($product1);
        $order->addProduct($product1);

        static::assertCount(1, $order->products());
        static::assertSame($product1->quantity * 2, $order->getProduct(1)->quantity);
    }

    public function test_adding_ingredients(): void
    {
        $order = new Order();
        $order->addIngredient($ingredient = new Ingredient(1, 100, 10, 5));

        static::assertEquals($ingredient, $order->getIngredient(1));
    }

    public function test_adding_ingredients_is_additive(): void
    {
        $ingredient1 = new Ingredient(1, 100, 10, 5);

        $order = new Order();

        $order->addIngredient($ingredient1);
        $order->addIngredient($ingredient1);

        static::assertCount(1, $order->ingredients());
        static::assertSame($ingredient1->required * 2, $order->getIngredient(1)->required);
    }

    public function test_getting_product(): void
    {
        $order = new Order();
        $order->addProduct($product = new Product($id = 1, 2));

        static::assertEquals($product, $order->getProduct($id));
    }

    public function test_getting_non_existent_product(): void
    {
        $order = new Order();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Product with ID 1 does not exist in the order.');

        $order->getProduct(1);
    }

    public function test_getting_ingredient(): void
    {
        $order = new Order();
        $order->addIngredient($ingredient = new Ingredient($id = 1, 100, 10, 5));

        static::assertEquals($ingredient, $order->getIngredient($id));
    }

    public function test_getting_non_existent_ingredient(): void
    {
        $order = new Order();

        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Ingredient with ID 1 does not exist in the order.');

        $order->getIngredient(1);
    }
}
