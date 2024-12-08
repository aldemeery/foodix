<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order as OrderModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psl\Type;

/** @mixin OrderModel */
class Order extends JsonResource
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
            'products' => Product::collection($this->whenLoaded('products')),
            'quantity' => $this->whenPivotLoaded(
                'order_product',
                fn (): int => Type\int()->assert($this->pivot->quantity),
            ),
        ];
    }
}
