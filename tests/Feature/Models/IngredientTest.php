<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Ingredient::class)]
class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function test_ingredient_belongs_to_many_products(): void
    {
        $ingredient = Ingredient::factory()->createOne();
        $products = Product::factory()->count($productsCount = 3)->create();
        $ingredient->products()->attach($products, ['amount' => $amount = 100]);
        $pivot = $ingredient->products()->first()?->getRelationValue('pivot');

        static::assertCount($productsCount, $ingredient->products()->get());
        static::assertInstanceOf(Product::class, $ingredient->products()->first());
        static::assertInstanceOf(Pivot::class, $pivot);
        static::assertSame($amount, $pivot->getAttributeValue('amount'));
    }
}
