<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Events\OrderCreated;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderCreated::class)]
class OrderCreatedTest extends TestCase
{
    public function test_initialization(): void
    {
        $event = new OrderCreated($orderId = 1);

        static::assertEquals($orderId, $event->orderId);
    }
}
