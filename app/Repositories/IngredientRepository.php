<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\IngredientRepository as IngredientRepositoryContract;
use App\Models\Ingredient;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class IngredientRepository implements IngredientRepositoryContract
{
    /**
     * Constructor.
     *
     * @param Builder<Ingredient> $query
     */
    public function __construct(
        private Builder $query,
    ) {
    }

    /**
     * Find and ingredient by its ID.
     *
     * @throws ModelNotFoundException<Ingredient>
     */
    public function find(int $id): Ingredient
    {
        return Ingredient::findOrFail($id);
    }

    /**
     * Only include ingredients matching the given IDs.
     *
     * @param array<int, int> $ids
     */
    public function whereIdIn(array $ids): self
    {
        return new self($this->query->clone()->whereIn('id', $ids));
    }

    /** Perform a batch update on the stock amounts of ingredients. */
    public function batchUpdateStock(Expression $expression): void
    {
        $this->query->update(['stock' => $expression]);
    }

    /**
     * Retrieve the collection of ingredients based on the current query constraints.
     *
     * @return Collection<int, Ingredient>
     */
    public function get(): Collection
    {
        return $this->query->get();
    }
}
