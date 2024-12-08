<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Ingredient as IngredientResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Models\Ingredient as IngredientModel;
use App\Models\Order as OrderModel;
use App\Models\Product as ProductModel;
use Illuminate\Http\Resources\MissingValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProductResource::class)]
class ProductTest extends TestCase
{
    public function test_mapping_to_array_with_orders(): void
    {
        $orders = OrderModel::factory()->count(2)->make()->each(function (OrderModel $order): void {
            $order->forceFill(['id' => mt_rand(1, 100)]);
            $order['pivot'] = $order->newPivot(
                $order,
                ['quantity' => mt_rand(1, 100)],
                'order_product',
                true,
            );
        });

        $model = ProductModel::factory()->makeOne()->forceFill(['id' => 1]);
        $model->setRelation('orders', $orders);

        $model['pivot'] = $model->newPivot(
            $model,
            ['quantity' => mt_rand(1, 100)],
            'order_product',
            true,
        );

        $resource = new ProductResource($model);

        static::assertEquals([
            'id' => $model->id,
            'name' => $model->name,
            'orders' => OrderResource::collection($model->orders),
            'ingredients' => IngredientResource::collection(new MissingValue()),
            'quantity' => $model->pivot->quantity, // @phpstan-ignore-line
            'amount' => new MissingValue(),
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }

    public function test_mapping_to_array_with_ingredients(): void
    {
        $ingredients = IngredientModel::factory()->count(2)->make()->each(function (IngredientModel $ingredient): void {
            $ingredient->forceFill(['id' => mt_rand(1, 100)]);
            $ingredient['pivot'] = $ingredient->newPivot(
                $ingredient,
                ['amount' => mt_rand(1, 100)],
                'ingredient_product',
                true,
            );
        });

        $model = ProductModel::factory()->makeOne()->forceFill(['id' => 1]);
        $model->setRelation('ingredients', $ingredients);

        $model['pivot'] = $model->newPivot(
            $model,
            ['amount' => mt_rand(1, 100)],
            'ingredient_product',
            true,
        );

        $resource = new ProductResource($model);

        static::assertEquals([
            'id' => $model->id,
            'name' => $model->name,
            'orders' => OrderResource::collection(new MissingValue()),
            'ingredients' => IngredientResource::collection($model->ingredients),
            'quantity' => new MissingValue(),
            'amount' => $model->pivot->amount, // @phpstan-ignore-line
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }

    public function test_mapping_to_array_without_conditional_data(): void
    {
        $model = ProductModel::factory()->makeOne()->forceFill(['id' => 1]);

        $resource = new ProductResource($model);

        static::assertEquals([
            'id' => $model->id,
            'name' => $model->name,
            'orders' => OrderResource::collection(new MissingValue()),
            'ingredients' => IngredientResource::collection(new MissingValue()),
            'quantity' => new MissingValue(),
            'amount' => new MissingValue(),
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }
}
