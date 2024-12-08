<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Models\Order as OrderModel;
use App\Models\Product as ProductModel;
use Illuminate\Http\Resources\MissingValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderResource::class)]
class OrderTest extends TestCase
{
    public function test_mapping_to_array(): void
    {
        $products = ProductModel::factory()->count(2)->make()->each(function (ProductModel $product): void {
            $product->forceFill(['id' => mt_rand(1, 100)]);
            $product['pivot'] = $product->newPivot(
                $product,
                ['quantity' => mt_rand(1, 100)],
                'order_product',
                true,
            );
        });

        $model = OrderModel::factory()->makeOne()->forceFill(['id' => 1]);
        $model->setRelation('products', $products);
        $model['pivot'] = $model->newPivot(
            $model,
            ['quantity' => mt_rand(1, 100)],
            'order_product',
            true,
        );

        $resource = new OrderResource($model);

        static::assertEquals([
            'id' => $model->id,
            'products' => ProductResource::collection($model->products),
            'quantity' => $model->pivot->quantity, // @phpstan-ignore-line
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }

    public function test_mapping_to_array_without_conditional_data(): void
    {
        $model = OrderModel::factory()->makeOne()->forceFill(['id' => 1]);

        $resource = new OrderResource($model);

        static::assertEquals([
            'id' => $model->id,
            'products' => ProductResource::collection(new MissingValue()),
            'quantity' => new MissingValue(),
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }
}
