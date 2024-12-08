<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Ingredient;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface IngredientRepository
{
    /**
     * Find and ingredient by its ID.
     *
     * @throws ModelNotFoundException<Ingredient>
     */
    public function find(int $id): Ingredient;

    /**
     * Only include ingredients matching the given IDs.
     *
     * @param array<int, int> $ids
     */
    public function whereIdIn(array $ids): self;

    /** Perform a batch update on the stock amounts of ingredients. */
    public function batchUpdateStock(Expression $expression): void;
}
