<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrderBuilder as OrderBuilderContract;
use App\DTOs\Ingredient as IngredientDTO;
use App\DTOs\Order;
use App\DTOs\Product as ProductDTO;
use App\Models\Ingredient;
use OutOfBoundsException;
use Psl\Iter;

class OrderBuilder implements OrderBuilderContract
{
    private Order $order;

    /** Constructor. */
    public function __construct()
    {
        $this->newOrder();
    }

    /**
     * Initialize a new order instance, resetting the builder
     * to prepare for creating a new order.
     */
    public function newOrder(): self
    {
        $this->order = new Order();

        return $this;
    }

    /**
     * Load an existing order into the builder for further modifications, allowing for
     * adjustments or additions to an existing order structure.
     */
    public function usingOrder(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /** Add a product to the current order being built. */
    public function addProduct(ProductDTO $product): self
    {
        $this->order->addProduct($product);

        return $this;
    }

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
    public function addIngredientModels(array $ingredients, int $productId): self
    {
        $product = $this->order->getProduct($productId);

        Iter\apply(
            $ingredients,
            function (Ingredient $ingredient) use ($product): void {
                $this->order->addIngredient(new IngredientDTO(
                    $ingredient->id,
                    $ingredient->stock,
                    $ingredient->threshold,
                    $product->quantity * $ingredient->pivot->amount,
                ));
            },
        );

        return $this;
    }

    /** Finalize the order construction and retrieve the resulting order DTO instance. */
    public function get(): Order
    {
        return $this->order;
    }
}
