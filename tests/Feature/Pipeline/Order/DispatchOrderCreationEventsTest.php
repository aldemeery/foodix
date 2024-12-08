<?php

declare(strict_types=1);

namespace Tests\Feature\Pipeline\Order;

use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\Events\OrderCreated;
use App\Models\Order;
use App\Pipeline\Order\DispatchOrderCreationEvents;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DispatchOrderCreationEvents::class)]
class DispatchOrderCreationEventsTest extends TestCase
{
    public function test_handling(): void
    {
        Event::fake();

        $payload = $this->createPayload();
        $step = new DispatchOrderCreationEvents();
        $next = fn (OrderProcessPayload $payload): OrderProcessPayload => $payload;

        $step->handle($payload, $next);

        Event::assertDispatched(
            OrderCreated::class,
            fn (OrderCreated $event): bool => $event->orderId === $payload->model->id,
        );
    }

    private function createPayload(): OrderProcessPayload
    {
        return new OrderProcessPayload(
            Order::factory()->makeOne()->forceFill(['id' => 1]),
            new OrderDTO(),
        );
    }
}
