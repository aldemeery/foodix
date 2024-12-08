<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OrderBuilder;
use App\Contracts\OrderProcessingStep;
use App\Contracts\OrderProcessor as OrderProcessorContract;
use App\Contracts\ProductRepository;
use App\DTOs\Order as OrderDTO;
use App\DTOs\OrderProcessPayload;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Container\Attributes\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Pipeline;
use Psl\Iter;
use Psl\Type;

class OrderProcessor implements OrderProcessorContract
{
    /**
     * Constructor.
     *
     * @param iterable<int, OrderProcessingStep> $steps
     */
    public function __construct(
        private ProductRepository $products,
        private OrderBuilder $orderBuilder,
        #[Tag(OrderProcessingStep::class)]
        private iterable $steps,
    ) {
    }

    /**
     * Process the given order data transfer object (DTO) and generate an order model.
     * This involves applying necessary transformations, validations, and operations
     * to turn the input order data into a persisted or ready-to-use order instance.
     */
    public function process(OrderDTO $order): Order
    {
        return DB::transaction(function () use ($order): Order {
            $products = $this->products->withLockedIngredients()->whereIdIn(
                $order->products()->keys()->all(),
            )->get();

            $payload = new OrderProcessPayload(
                new Order(),
                $this->buildOrder($order, $products),
            );

            return Type\instance_of(OrderProcessPayload::class)->assert(
                Pipeline::send($payload)->through(iterator_to_array($this->steps))->thenReturn(),
            )->model;
        });
    }

    /**
     * Add ingredients to the order.
     *
     * @param Collection<int, Product> $products
     */
    private function buildOrder(OrderDTO $order, Collection $products): OrderDTO
    {
        return Iter\reduce(
            $products,
            fn (OrderBuilder $orderBuilder, Product $product): OrderBuilder => $orderBuilder->addIngredientModels(
                $product->ingredients->all(),
                $product->id,
            ),
            $this->orderBuilder->usingOrder($order),
        )->get();
    }
}
