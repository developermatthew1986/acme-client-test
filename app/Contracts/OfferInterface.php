<?php

namespace App\Contracts;

interface OfferInterface
{
    /**
     * Calculate the discount for this offer
     *
     * @param array $items
     * @return float
     */
    public function calculateDiscount(array $items): float;

    /**
     * Get the offer type identifier
     *
     * @return string
     */
    public function getType(): string;
}
