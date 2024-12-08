<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Register any application services. */
    public function register(): void
    {
        $this->registerBindings();
        $this->registerTags();
    }

    /** Bootstrap any application services. */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Model::shouldBeStrict();
    }

    private function registerBindings(): void
    {
        $this->app->bind(
            \App\Contracts\OrderProcessor::class,
            \App\Services\OrderProcessor::class,
        );

        $this->app->bind(
            \App\Contracts\OrderBuilder::class,
            \App\Services\OrderBuilder::class,
        );

        $this->app->bind(
            \App\Contracts\ProductRepository::class,
            function (): \App\Contracts\ProductRepository {
                return new \App\Repositories\ProductRepository(\App\Models\Product::query());
            },
        );

        $this->app->bind(
            \App\Contracts\IngredientRepository::class,
            function (): \App\Contracts\IngredientRepository {
                return new \App\Repositories\IngredientRepository(\App\Models\Ingredient::query());
            },
        );

        $this->app->bind(
            \App\Contracts\OrderRepository::class,
            function (): \App\Contracts\OrderRepository {
                return new \App\Repositories\OrderRepository();
            },
        );
    }

    private function registerTags(): void
    {
        $this->app->tag(
            [
                \App\Pipeline\Order\ValidateOrder::class,
                \App\Pipeline\Order\UpdateStock::class,
                \App\Pipeline\Order\PersistOrder::class,
                \App\Pipeline\Order\HandleLowStock::class,
                \App\Pipeline\Order\DispatchOrderCreationEvents::class,
            ],
            \App\Contracts\OrderProcessingStep::class,
        );
    }
}
