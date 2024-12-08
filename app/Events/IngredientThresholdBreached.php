<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class IngredientThresholdBreached
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /** Constructor. */
    public function __construct(
        public readonly int $ingredientId,
    ) {
    }
}
