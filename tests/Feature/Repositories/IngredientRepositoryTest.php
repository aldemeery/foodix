<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(IngredientRepository::class)]
class IngredientRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_finding_existing_ingredients(): void
    {
        Ingredient::factory()->createOne(['id' => $id = 1]);

        $repository = $this->createRepository();

        $ingredient = $repository->find($id);

        static::assertSame($id, $ingredient->id);
    }

    public function test_finding_nonexistent_ingredients(): void
    {
        $repository = $this->createRepository();

        $this->expectExceptionMessage('No query results for model [App\Models\Ingredient] 1');
        $this->expectException(ModelNotFoundException::class);

        $repository->find(1);
    }

    public function test_filtering_ingredients_by_id(): void
    {
        Ingredient::factory()->createOne();
        Ingredient::factory()->createOne();

        $repository = $this->createRepository();

        $filteredRepository = $repository->whereIdIn([1]);

        static::assertCount(1, $filteredRepository->get());
    }

    public function test_batch_updating_stock(): void
    {
        Ingredient::factory()->createOne(['stock' => 10]);
        Ingredient::factory()->createOne(['stock' => 20]);

        $repository = $this->createRepository();

        $repository->batchUpdateStock(
            DB::raw('CASE WHEN id = 1 THEN stock - 5 WHEN id = 2 THEN stock - 10 ELSE stock END'),
        );

        static::assertSame(5, Ingredient::findOrFail(1)->stock);
        static::assertSame(10, Ingredient::findOrFail(2)->stock);
    }

    public function test_getting_all_ingredients(): void
    {
        Ingredient::factory()->count(3)->create();

        $repository = $this->createRepository();

        static::assertCount(3, $repository->get());
    }

    private function createRepository(): IngredientRepository
    {
        return new IngredientRepository(Ingredient::query());
    }
}
