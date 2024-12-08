<?php

declare(strict_types=1);

namespace App\Pipeline\Order;

use App\Contracts\OrderProcessingStep;
use App\DTOs\Ingredient;
use App\DTOs\OrderProcessPayload;
use Closure;
use Illuminate\Support\Facades\Validator;

class ValidateOrder implements OrderProcessingStep
{
    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload
    {
        Validator::validate($this->data($payload), $this->rules(), $this->messages());

        return $next($payload);
    }

    /** @return array<string, int> */
    private function data(OrderProcessPayload $payload): array
    {
        return [
            'ingredients' => $payload->dto->ingredients()->where(
                fn (Ingredient $ingredient): bool => $ingredient->doesNotHaveEnoughStock(),
            )->count(),
        ];
    }

    /** @return array<string, list<string>> */
    private function rules(): array
    {
        return [
            'ingredients' => ['integer', 'size:0'],
        ];
    }

    /** @return array<string, string> */
    private function messages(): array
    {
        return [
            'ingredients' => __('The order cannot be processed due to insufficient ingredients.'),
        ];
    }
}
