<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Models\Ingredient;
use App\Notifications\IngredientThresholdBreached;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(IngredientThresholdBreached::class)]
class IngredientThresholdBreachedTest extends TestCase
{
    public function test_via(): void
    {
        $notification = new IngredientThresholdBreached(Ingredient::factory()->make());

        static::assertEquals(['mail'], $notification->via((object) []));
    }

    public function test_to_mail(): void
    {
        $ingredient = Ingredient::factory()->make();

        $notification = new IngredientThresholdBreached($ingredient);

        $mail = $notification->toMail((object) []);

        static::assertEquals(
            "{$ingredient->name} Threshold Breached",
            $mail->subject,
        );
        static::assertEquals(
            "The amount of {$ingredient->name} in stock has fallen below the threshold.",
            $mail->introLines[0],
        );
        static::assertEquals(
            'Manage Stock',
            $mail->actionText,
        );
    }
}
