<?php

declare(strict_types=1);

namespace App\Pipeline\Order;

use App\Contracts\IngredientRepository;
use App\Contracts\OrderProcessingStep;
use App\DTOs\Ingredient;
use App\DTOs\OrderProcessPayload;
use Closure;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Psl\Iter;

class UpdateStock implements OrderProcessingStep
{
    /** Constructor. */
    public function __construct(
        private IngredientRepository $ingredients,
    ) {
    }

    /** @param Closure(OrderProcessPayload): OrderProcessPayload $next */
    public function handle(OrderProcessPayload $payload, Closure $next): OrderProcessPayload
    {
        $this->ingredients->whereIdIn(
            $payload->dto->ingredients()->keys()->all(),
        )->batchUpdateStock(
            $this->compileExpression($payload->dto->ingredients()->all()),
        );

        return $next($payload);
    }

    /**
     * Compiles a raw SQL CASE expression to update ingredient stock levels.
     *
     * Constructs a SQL CASE expression that deducts specified amounts from the stock
     * of ingredients based on their IDs. If an ID is not matched, the stock remains unchanged.
     *
     * The benefit of this approach is that it allows for a single query to update multiple
     * rows with different values at once.
     *
     * Example generated SQL:
     * `CASE WHEN id = 1 THEN stock - 5 WHEN id = 2 THEN stock - 10 ELSE stock END`
     *
     * @param array<int, Ingredient> $ingredients An associative array where keys are ingredient IDs
     *                                            and values are the amounts to deduct from the stock.
     */
    private function compileExpression(array $ingredients): Expression
    {
        return DB::raw(
            sprintf(
                'CASE %s ELSE stock END',
                Iter\reduce(
                    $ingredients,
                    fn (string $carry, Ingredient $ingredient): string => sprintf(
                        '%s WHEN id = %d THEN stock - %d',
                        $carry,
                        $ingredient->id,
                        $ingredient->required,
                    ),
                    '',
                ),
            ),
        );
    }
}
