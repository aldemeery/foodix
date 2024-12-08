<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Contracts\OrderBuilder;
use App\DTOs\Order;
use App\DTOs\Product;
use Illuminate\Foundation\Http\FormRequest;
use Psl\Iter;
use Psl\Type;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    /** Using a given OrderBuilder, coerce the validated request data into an Order instance. */
    public function coerce(OrderBuilder $orderBuilder): Order
    {
        return Iter\reduce(
            $this->collect('products'),
            function (OrderBuilder $orderBuilder, $product): OrderBuilder {
                $product = Type\dict(Type\string(), Type\int())->assert($product);

                return $orderBuilder->addProduct(new Product($product['product_id'], $product['quantity']));
            },
            $orderBuilder->newOrder(),
        )->get();
    }
}
