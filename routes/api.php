<?php

declare(strict_types=1);

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => ['Foodix' => '0.1.0']);

Route::apiResource('orders', OrderController::class)->only('store');
