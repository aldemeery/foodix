<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\IngredientThresholdBreached;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IngredientThresholdBreached::class)]
class IngredientThresholdBreachedTest extends TestCase
{
    public function test_initialization(): void
    {
        $event = new IngredientThresholdBreached($ingredientId = 1);

        static::assertEquals($ingredientId, $event->ingredientId);
    }
}
