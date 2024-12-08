<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Product::class)]
class ProductTest extends TestCase
{
    public function test_increasing_quantity(): void
    {
        $product = new Product(1, 10);
        $updatedProduct = $product->increaseQuantity(5);

        static::assertEquals(10, $product->quantity);
        static::assertEquals(15, $updatedProduct->quantity);
    }
}
