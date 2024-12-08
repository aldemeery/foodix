<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Product as ProductModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psl\Type;

/** @mixin ProductModel */
class Product extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'orders' => Order::collection($this->whenLoaded('orders')),
            'ingredients' => Ingredient::collection($this->whenLoaded('ingredients')),
            'quantity' => $this->whenPivotLoaded(
                'order_product',
                fn (): int => Type\int()->assert($this->pivot->quantity),
            ),
            'amount' => $this->whenPivotLoaded(
                'ingredient_product',
                fn (): int => Type\int()->assert($this->pivot->amount),
            ),
        ];
    }
}
