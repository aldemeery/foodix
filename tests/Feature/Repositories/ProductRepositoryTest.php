<?php

declare(strict_types=1);

namespace Tests\Feature\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use PHPUnit\Framework\Attributes\CoversClass;
use Psl\Type;
use Tests\TestCase;

#[CoversClass(ProductRepository::class)]
class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function test_eager_loading_locked_ingredients(): void
    {
        $query = m::mock(Product::query());
        $query->shouldReceive('clone')
            ->once()
            ->andReturnSelf();
        $query->shouldReceive('with')
            ->once()
            ->with(
                'ingredients',
                m::on(function (Closure $closure): bool {
                    $mockBelongsToMany = m::mock(BelongsToMany::class);
                    $mockBelongsToMany->shouldReceive('lockForUpdate')
                        ->once()
                        ->andReturnSelf();

                    $closure($mockBelongsToMany);

                    return true;
                }),
            )
            ->andReturnSelf();

        $repository = new ProductRepository(Type\instance_of(Builder::class)->assert($query));

        $repository->withLockedIngredients();
    }

    public function test_filtering_products_by_id(): void
    {
        Product::factory()->createOne();
        Product::factory()->createOne();

        $repository = $this->createRepository();

        $filteredRepository = $repository->whereIdIn([1]);

        static::assertCount(1, $filteredRepository->get());
    }

    public function test_getting_all_products(): void
    {
        Product::factory()->createOne();
        Product::factory()->createOne();

        $repository = $this->createRepository();

        static::assertCount(2, $repository->get());
    }

    private function createRepository(): ProductRepository
    {
        return new ProductRepository(Product::query());
    }
}
