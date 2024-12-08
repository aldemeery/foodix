<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Ingredient as IngredientModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Psl\Type;

/** @mixin IngredientModel */
class Ingredient extends JsonResource
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
            'stock' => $this->stock,
            'threshold' => $this->threshold,
            'products' => Product::collection($this->whenLoaded('products')),
            'amount' => $this->whenPivotLoaded(
                'ingredient_product',
                fn (): int => Type\int()->assert($this->pivot->amount),
            ),
        ];
    }
}
