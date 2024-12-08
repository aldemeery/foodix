<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /** Seed the application's database. */
    public function run(): void
    {
        User::factory()->createOne();

        $beef = Ingredient::factory()->createOne(['name' => 'Beef', 'stock' => 20000, 'threshold' => 10000]);
        $cheese = Ingredient::factory()->createOne(['name' => 'Cheese', 'stock' => 5000, 'threshold' => 2500]);
        $onion = Ingredient::factory()->createOne(['name' => 'Onion', 'stock' => 1000, 'threshold' => 500]);

        $burger = Product::factory()->createOne(['name' => 'Burger']);
        $pizza = Product::factory()->createOne(['name' => 'Pizza']);

        $burger->ingredients()->attach([
            $beef->id => ['amount' => 150],
            $cheese->id => ['amount' => 30],
            $onion->id => ['amount' => 20],
        ]);

        $pizza->ingredients()->attach([
            $beef->id => ['amount' => 100],
            $cheese->id => ['amount' => 50],
            $onion->id => ['amount' => 10],
        ]);
    }
}
