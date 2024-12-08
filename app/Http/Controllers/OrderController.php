<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\OrderBuilder;
use App\Contracts\OrderProcessor;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\Order as OrderResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class OrderController extends Controller
{
    /** Constructor. */
    public function __construct(
        private OrderProcessor $orders,
        private OrderBuilder $orderBuilder,
    ) {
    }

    /**
     * Handles the creation of a new order.
     *
     * Validates the incoming request, processes the order, and returns a JSON response.
     * If processing fails, appropriate error handling is performed.
     *
     * @throws HttpException       If the order cannot be processed.
     * @throws ValidationException If the validation fails during processing.
     */
    public function store(StoreOrderRequest $request): JsonResource
    {
        return rescue(
            fn (): JsonResource => new OrderResource(
                $this->orders->process(
                    $request->coerce($this->orderBuilder),
                ),
            ),
            fn (Throwable $th): never => $th instanceof ValidationException ? throw $th : abort(
                Response::HTTP_SERVICE_UNAVAILABLE,
                __('Failed to process the order, please try again later'),
            ),
        );
    }
}
