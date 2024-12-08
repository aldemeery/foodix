<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Contracts\OrderBuilder;
use App\Contracts\OrderProcessingStep;
use App\Contracts\ProductRepository;
use App\DTOs\Order as OrderDTO;
use App\DTOs\Product as ProductDTO;
use App\Models\Ingredient;
use App\Models\Product;
use App\Services\OrderProcessor;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Pipeline;
use PHPUnit\Framework\Attributes\CoversClass;
use Psl\Type;
use Tests\TestCase;

#[CoversClass(OrderProcessor::class)]
class OrderProcessorTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_processing(): void
    {
        $product = Product::factory()->createOne();
        $ingredient = Ingredient::factory()->createOne();
        $product->ingredients()->attach($ingredient, ['amount' => $amount = 2]);

        $orderProcessor = $this->getOrderProcessor();

        $orderDto = new OrderDTO();
        $orderDto->addProduct(new ProductDTO($product->id, $quantity = 2));

        $order = $orderProcessor->process($orderDto);

        $this->assertDatabaseHas('orders', ['id' => $order->id]);
        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
        ]);
        $this->assertDatabaseHas('ingredients', [
            'id' => $ingredient->id,
            'stock' => $ingredient->stock - $amount * $quantity,
        ]);
    }

    public function test_processing_is_transactional(): void
    {
        Pipeline::partialMock()->shouldReceive('thenReturn')->andThrow(new Exception('Test exception'));

        $product = Product::factory()->createOne();
        $ingredient = Ingredient::factory()->createOne();
        $product->ingredients()->attach($ingredient, ['amount' => $amount = 2]);

        $orderProcessor = $this->getOrderProcessor();

        $orderDto = new OrderDTO();
        $orderDto->addProduct(new ProductDTO($product->id, $quantity = 2));

        try {
            $orderProcessor->process($orderDto);
        } catch (Exception $e) {
            // Do nothing...
        }

        $this->assertDatabaseMissing('orders', ['product_id' => $product->id]);
        $this->assertDatabaseMissing('order_product', ['product_id' => $product->id]);
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'stock' => $ingredient->stock]);
    }

    public function getOrderProcessor(): OrderProcessor
    {
        return new OrderProcessor(
            $this->app->make(ProductRepository::class),
            $this->app->make(OrderBuilder::class),
            Type\iterable(Type\int(), Type\instance_of(OrderProcessingStep::class))->assert(
                $this->app->tagged(OrderProcessingStep::class),
            ),
        );
    }
}
