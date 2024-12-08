<?php

declare(strict_types=1);

namespace App\DTOs;

use Illuminate\Support\Collection;
use OutOfBoundsException;

final class Order
{
    /**
     * An associative array of ingredients indexed by their IDs.
     *
     * @var array<int, Ingredient>
     */
    private array $ingredients = [];

    /**
     * An associative array of products indexed by their IDs.
     *
     * @var array<int, Product>
     */
    private array $products = [];

    /**
     * Retrieve a collection of all products in the order.
     *
     * @return Collection<int, Product>
     */
    public function products(): Collection
    {
        return new Collection($this->products);
    }

    /**
     * Retrieve a collection of all ingredients in the order.
     *
     * @return Collection<int, Ingredient>
     */
    public function ingredients(): Collection
    {
        return new Collection($this->ingredients);
    }

    /**
     * Add a product to the order, updating its quantity if it already exists.
     * The product quantity is incremented by its existing amount in the order.
     */
    public function addProduct(Product $product): self
    {
        $this->products[$product->id] = $product->increaseQuantity(
            $this->products[$product->id]->quantity ?? 0
        );

        return $this;
    }

    /**
     * Add an ingredient to the order, updating its required quantity if it already exists.
     * The required quantity is incremented by the existing required amount in the order.
     */
    public function addIngredient(Ingredient $ingredient): self
    {
        $this->ingredients[$ingredient->id] = $ingredient->require(
            $this->ingredients[$ingredient->id]->required ?? 0
        );

        return $this;
    }

    /**
     * Retrieve a specific product from the order by its ID.
     *
     * @throws OutOfBoundsException
     */
    public function getProduct(int $id): Product
    {
        if (!isset($this->products[$id])) {
            throw new OutOfBoundsException("Product with ID $id does not exist in the order.");
        }

        return $this->products[$id];
    }

    /**
     * Retrieve a specific ingredient from the order by its ID.
     *
     * @throws OutOfBoundsException
     */
    public function getIngredient(int $id): Ingredient
    {
        if (!isset($this->ingredients[$id])) {
            throw new OutOfBoundsException("Ingredient with ID $id does not exist in the order.");
        }

        return $this->ingredients[$id];
    }
}
