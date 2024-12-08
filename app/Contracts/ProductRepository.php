<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductRepository
{
    /**
     * Modify the query to include a PESSIMISTIC lock on the associated ingredients.
     * This ensures that ingredient data related to the products is locked
     * for consistency during concurrent operations.
     */
    public function withLockedIngredients(): self;

    /**
     * Only include products matching the given IDs.
     *
     * @param array<int, int> $ids
     */
    public function whereIdIn(array $ids): self;

    /**
     * Retrieve the collection of products based on the current query constraints.
     *
     * @return Collection<int, Product>
     */
    public function get(): Collection;
}
