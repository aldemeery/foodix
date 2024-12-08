<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Contracts\OrderProcessor;
use App\Http\Controllers\OrderController;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Models\Ingredient;
use App\Models\Product;
use Closure;
use Exception;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery as m;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(OrderController::class)]
#[CoversClass(StoreOrderRequest::class)]
class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }

    public function test_creating_orders_successfully(): void
    {
        $this->seed();

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                ['product_id' => $productId = 1, 'quantity' => $quantity = 2],
            ],
        ]);

        $response->assertCreated();
        $response->assertJson(
            fn (AssertableJson $json) => $json
                ->has(
                    'data',
                    fn (AssertableJson $json) => $json
                        ->has('id')
                        ->has(
                            'products',
                            fn (AssertableJson $json) => $json
                                ->has(1)
                                ->first(
                                    fn (AssertableJson $json) => $json
                                        ->where('id', $productId)
                                        ->has('name')
                                        ->where('quantity', $quantity)
                                )
                        )
                )
        );
    }

    public function test_creating_orders_fails_gracefully(): void
    {
        $this->seed();

        $this->instance(
            OrderProcessor::class,
            m::mock(OrderProcessor::class, function (MockInterface $mock): void {
                $mock->shouldReceive('process')->andThrow(new Exception());
            }),
        );

        $response = $this->postJson(route('orders.store'), [
            'products' => [
                ['product_id' => 1, 'quantity' => 2],
            ],
        ]);

        $response->assertServiceUnavailable();
        $response->assertJson(['message' => 'Failed to process the order, please try again later']);
    }

    public static function get_validation_data(): Generator
    {
        yield 'no products' => [
            fn (): array => [],
            [
                'products' => ['The products field is required.'],
            ],
        ];

        yield 'invalid products type' => [
            fn (): array => ['products' => 'invalid'],
            [
                'products' => ['The products field must be an array.'],
            ],
        ];

        yield 'empty products' => [
            fn (): array => ['products' => []],
            [
                'products' => ['The products field is required.'],
            ],
        ];

        yield 'non-existing products' => [
            fn (): array => ['products' => [['product_id' => 999, 'quantity' => 1]]],
            [
                'products.0.product_id' => ['The selected products.0.product_id is invalid.'],
            ],
        ];

        yield 'missing product id' => [
            fn (): array => ['products' => [['quantity' => 1]]],
            [
                'products.0.product_id' => ['The products.0.product_id field is required.'],
            ],
        ];

        yield 'invalid product id type' => [
            fn (): array => ['products' => [['product_id' => 'invalid', 'quantity' => 1]]],
            [
                'products.0.product_id' => ['The products.0.product_id field must be an integer.'],
            ],
        ];

        yield 'duplicate product id' => [
            fn (): array => [
                'products' => [
                    ['product_id' => 1, 'quantity' => 1],
                    ['product_id' => 1, 'quantity' => 1],
                ],
            ],
            [
                'products.0.product_id' => ['The products.0.product_id field has a duplicate value.'],
                'products.1.product_id' => ['The products.1.product_id field has a duplicate value.'],
            ],
        ];

        yield 'missing quantity' => [
            function (): array {
                $product = Product::factory()->createOne();

                return ['products' => [['product_id' => $product->id]]];
            },
            [
                'products.0.quantity' => ['The products.0.quantity field is required.'],
            ],
        ];

        yield 'invalid quantity type' => [
            function (): array {
                $product = Product::factory()->createOne();

                return ['products' => [['product_id' => $product->id, 'quantity' => 'invalid']]];
            },
            [
                'products.0.quantity' => ['The products.0.quantity field must be an integer.'],
            ],
        ];

        yield 'invalid quantity value' => [
            function (): array {
                $product = Product::factory()->createOne();

                return ['products' => [['product_id' => $product->id, 'quantity' => 0]]];
            },
            [
                'products.0.quantity' => ['The products.0.quantity field must be at least 1.'],
            ],
        ];

        yield 'insufficient ingredients' => [
            function (): array {
                $product = Product::factory()->createOne();
                $ingredient = Ingredient::factory()->createOne([
                    'stock' => 5,
                ]);

                $product->ingredients()->attach($ingredient, ['amount' => 10]);

                return ['products' => [['product_id' => $product->id, 'quantity' => 1]]];
            },
            [
                'ingredients' => ['The order cannot be processed due to insufficient ingredients.'],
            ],
        ];
    }

    /**
     * @param Closure(): array<string, mixed> $data
     * @param array<string, list<string>>     $errors
     */
    #[DataProvider('get_validation_data')]
    public function test_validation(Closure $data, array $errors): void
    {
        $response = $this->postJson(route('orders.store'), $data());

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors($errors);
    }
}
