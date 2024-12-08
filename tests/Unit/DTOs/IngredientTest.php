<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\Ingredient;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ingredient::class)]
class IngredientTest extends TestCase
{
    public static function get_checking_threshold_breach_data(): Generator
    {
        yield 'stock goes below threshold' => [
            new Ingredient(id: 1, stock: 30, threshold: 20, required: 20),
            true,
        ];

        yield 'stock at threshold' => [
            new Ingredient(id: 1, stock: 30, threshold: 30, required: 10),
            true,
        ];

        yield 'stock lands at threshold' => [
            new Ingredient(id: 1, stock: 30, threshold: 20, required: 10),
            false,
        ];

        yield 'stock lands above threshold' => [
            new Ingredient(id: 1, stock: 30, threshold: 20, required: 5),
            false,
        ];
    }

    #[DataProvider('get_checking_threshold_breach_data')]
    public function test_checking_threshold_breach(Ingredient $ingredient, bool $expected): void
    {
        static::assertSame($expected, $ingredient->breachesThreshold());
    }

    public function test_checking_stock_sufficiency(): void
    {
        $ingredientWithSufficientStock = new Ingredient(id: 1, stock: 20, threshold: 20, required: 20);
        $ingredientWithInsufficientStock = new Ingredient(id: 1, stock: 20, threshold: 20, required: 40);

        static::assertTrue($ingredientWithSufficientStock->hasEnoughStock());
        static::assertFalse($ingredientWithSufficientStock->doesNotHaveEnoughStock());
        static::assertTrue($ingredientWithInsufficientStock->doesNotHaveEnoughStock());
        static::assertFalse($ingredientWithInsufficientStock->hasEnoughStock());
    }

    public function test_updating_required_quantity(): void
    {
        $ingredient = new Ingredient(id: 1, stock: 20, threshold: 20, required: 20);
        $updatedIngredient = $ingredient->require(10);

        static::assertSame(20, $ingredient->required);
        static::assertSame(30, $updatedIngredient->required);
    }
}
