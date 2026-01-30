<?php

namespace App\Services\Offers;

use App\Contracts\OfferInterface;

class BuyOneGetSecondHalfPriceOffer implements OfferInterface
{
    public function __construct(
        protected string $productCode
    ) {}

    /**
     * Calculate discount for buy one get second half price offer
     *
     * @param array $items
     * @return float
     */
    public function calculateDiscount(array $items): float
    {
        $matchingItems = collect($items)->filter(function ($product) {
            return $this->getProductCode($product) === $this->productCode;
        });

        $count = $matchingItems->count();
        $pairs = intdiv($count, 2);

        $price = $this->getProductPrice($matchingItems->first());
        
        return round($pairs * ($price / 2), 2);
    }

    /**
     * Get the offer type identifier
     *
     * @return string
     */
    public function getType(): string
    {
        return 'buy_one_get_second_half_price';
    }

    /**
     * Get product code from product (array or object)
     *
     * @param mixed $product
     * @return string
     */
    protected function getProductCode(mixed $product): string
    {
        return is_array($product) ? $product['code'] : $product->code;
    }

    /**
     * Get product price from product (array or object)
     *
     * @param mixed $product
     * @return float
     */
    protected function getProductPrice(mixed $product): float
    {
        return is_array($product) ? $product['price'] : $product->price;
    }
}
