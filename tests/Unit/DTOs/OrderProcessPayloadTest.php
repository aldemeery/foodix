<?php

declare(strict_types=1);

namespace Tests\Unit\DTOs;

use App\DTOs\OrderProcessPayload;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderProcessPayload::class)]
class OrderProcessPayloadTest extends TestCase
{
    public function test_initialization(): void
    {
        $model = new \App\Models\Order();
        $dto = new \App\DTOs\Order();

        $payload = new OrderProcessPayload($model, $dto);

        static::assertEquals($model, $payload->model);
        static::assertEquals($dto, $payload->dto);
    }
}
