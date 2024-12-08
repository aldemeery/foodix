<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\Ingredient as IngredientResource;
use App\Http\Resources\Product as ProductResource;
use App\Models\Ingredient as IngredientModel;
use App\Models\Product as ProductModel;
use Illuminate\Http\Resources\MissingValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IngredientResource::class)]
class IngredientTest extends TestCase
{
    public function test_mapping_to_array(): void
    {
        $products = ProductModel::factory()->count(2)->make()->each(function (ProductModel $product): void {
            $product->forceFill(['id' => mt_rand(1, 100)]);
            $product['pivot'] = $product->newPivot(
                $product,
                ['amount' => mt_rand(1, 100)],
                'ingredient_product',
                true,
            );
        });

        $model = IngredientModel::factory()->makeOne()->forceFill(['id' => 1]);
        $model->setRelation('products', $products);
        $model['pivot'] = $model->newPivot(
            $model,
            ['amount' => mt_rand(1, 100)],
            'ingredient_product',
            true,
        );

        $resource = new IngredientResource($model);

        static::assertEquals([
            'id' => $model->id,
            'name' => $model->name,
            'stock' => $model->stock,
            'threshold' => $model->threshold,
            'products' => ProductResource::collection($model->products),
            'amount' => $model->pivot->amount, // @phpstan-ignore-line
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }

    public function test_mapping_to_array_without_conditional_data(): void
    {
        $model = IngredientModel::factory()->makeOne()->forceFill(['id' => 1]);

        $resource = new IngredientResource($model);

        static::assertEquals([
            'id' => $model->id,
            'name' => $model->name,
            'stock' => $model->stock,
            'threshold' => $model->threshold,
            'products' => ProductResource::collection(new MissingValue()),
            'amount' => new MissingValue(),
        ], $resource->toArray(new \Illuminate\Http\Request()));
    }
}
