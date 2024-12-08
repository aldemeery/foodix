<?php

declare(strict_types=1);

namespace App\DTOs;

final readonly class Ingredient
{
    /** Constructor. */
    public function __construct(
        public int $id,
        public int $stock,
        public int $threshold,
        public int $required,
    ) {
    }

    /**
     * Determine if the stock breaches the threshold after accounting for the required quantity.
     * A breach occurs if the remaining stock after usage falls below the threshold.
     *
     * If the stock is already below the threshold, this will return false.
     */
    public function breachesThreshold(): bool
    {
        return $this->thresholdNotBreachedYet() && ($this->stock - $this->required) < $this->threshold;
    }

    /** Determine if the current stock is sufficient to fulfill the required amount. */
    public function hasEnoughStock(): bool
    {
        return $this->stock >= $this->required;
    }

    /** Determine if the current stock is insufficient to fulfill the required amount. */
    public function doesNotHaveEnoughStock(): bool
    {
        return !$this->hasEnoughStock();
    }

    /**
     * Create a new instance of Ingredient with an updated required quantity.
     * This does not modify the original instance as the class is immutable.
     */
    public function require(int $required): self
    {
        return new self(
            $this->id,
            $this->stock,
            $this->threshold,
            $this->required + $required,
        );
    }

    /** Determine if the threshold has not yet been breached by the current stock. */
    private function thresholdNotBreachedYet(): bool
    {
        return $this->stock >= $this->threshold;
    }
}
