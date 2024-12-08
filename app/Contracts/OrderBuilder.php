<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOs\Order;
use App\DTOs\Product;
use App\Models\Ingredient;
use OutOfBoundsException;

interface OrderBuilder
{
    /**
     * Initialize a new order instance, resetting the builder
     * to prepare for creating a new order.
     */
    public function newOrder(): self;

    /**
     * Load an existing order into the builder for further modifications, allowing for
     * adjustments or additions to an existing order structure.
     */
    public function usingOrder(Order $order): self;

    /** Add a product to the current order being built. */
    public function addProduct(Product $product): self;

    /**
     * Create and add ingredient DTOs to the current order, given a list of ingredient models
     * and the product ID they are associated with.
     *
     * The product ID is necessary for determining the total amount of each ingredient, as ingredients
     * amounts differ based on the product they are associated with.
     *
     * @param array<int, Ingredient> $ingredients
     *
     * @throws OutOfBoundsException
     */
    public function addIngredientModels(array $ingredients, int $productId): self;

    /** Finalize the order construction and retrieve the resulting order DTO instance. */
    public function get(): Order;
}
