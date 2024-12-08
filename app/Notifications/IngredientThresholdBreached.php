<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IngredientThresholdBreached extends Notification
{
    use Queueable;

    /** Constructor. */
    public function __construct(
        private Ingredient $ingredient,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /** Get the mail representation of the notification. */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("{$this->ingredient->name} Threshold Breached")
            ->line("The amount of {$this->ingredient->name} in stock has fallen below the threshold.")
            ->action('Manage Stock', url('/'));
    }
}
