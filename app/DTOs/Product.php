<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class Product
{
    /** Constructor. */
    public function __construct(
        public int $id,
        public int $quantity,
    ) {
    }

    /**
     * Create a new instance of the Product class with an increased quantity.
     * This method does not modify the original instance as the class is immutable.
     */
    public function increaseQuantity(int $quantity): self
    {
        return new self(
            $this->id,
            $this->quantity + $quantity,
        );
    }
}
