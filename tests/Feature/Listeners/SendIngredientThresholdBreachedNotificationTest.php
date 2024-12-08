<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Contracts\IngredientRepository;
use App\Events\IngredientThresholdBreached;
use App\Listeners\SendIngredientThresholdBreachedNotification;
use App\Models\Ingredient;
use App\Models\User;
use App\Notifications\IngredientThresholdBreached as IngredientThresholdBreachedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SendIngredientThresholdBreachedNotification::class)]
class SendIngredientThresholdBreachedNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function test_handling(): void
    {
        Notification::fake();

        $ingredient = Ingredient::factory()->createOne();
        $user = User::factory()->createOne();

        $respository = m::mock(IngredientRepository::class);
        $respository->shouldReceive('find')->once()->andReturn($ingredient);

        $listener = new SendIngredientThresholdBreachedNotification($respository);
        $event = new IngredientThresholdBreached(1);

        $listener->handle($event);

        Notification::assertSentTo(
            [$user],
            IngredientThresholdBreachedNotification::class
        );
    }
}
