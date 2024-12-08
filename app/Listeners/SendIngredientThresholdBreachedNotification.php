<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Contracts\IngredientRepository;
use App\Events\IngredientThresholdBreached;
use App\Models\User;
use App\Notifications\IngredientThresholdBreached as IngredientThresholdBreachedNotification;
use Illuminate\Support\Facades\Notification;

class SendIngredientThresholdBreachedNotification
{
    /** Constructor. */
    public function __construct(
        private IngredientRepository $ingredients,
    ) {
    }

    /** Handle the event. */
    public function handle(IngredientThresholdBreached $event): void
    {
        Notification::send(
            $this->getUsers(),
            new IngredientThresholdBreachedNotification(
                $this->ingredients->find($event->ingredientId),
            ),
        );
    }

    /**
     * Get the users who should receive this notification.
     *
     * @return array<int, User>
     */
    private function getUsers(): array
    {
        // Let's just return the first user in the database for now...

        return [User::firstOrFail()];
    }
}
