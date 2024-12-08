<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\ProductRepository as ProductRepositoryContract;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class ProductRepository implements ProductRepositoryContract
{
    /**
     * Constructor.
     *
     * @param Builder<Product> $query
     */
    public function __construct(
        private Builder $query,
    ) {
    }

    /**
     * Modify the query to include a PESSIMISTIC lock on the associated ingredients.
     * This ensures that ingredient data related to the products is locked
     * for consistency during concurrent operations.
     */
    public function withLockedIngredients(): self
    {
        return new self($this->query->clone()->with(
            'ingredients',
            function (BelongsToMany $ingredients): void {
                $ingredients->lockForUpdate();
            },
        ));
    }

    /**
     * Only include products matching the given IDs.
     *
     * @param array<int, int> $ids
     */
    public function whereIdIn(array $ids): self
    {
        return new self($this->query->clone()->whereIn('id', $ids));
    }

    /**
     * Retrieve the collection of products based on the current query constraints.
     *
     * @return Collection<int, Product>
     */
    public function get(): Collection
    {
        return $this->query->get();
    }
}
