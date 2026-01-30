<?php

namespace App\Services;

use App\Contracts\OfferInterface;
use App\Exceptions\ProductNotFoundException;
use Illuminate\Support\Collection;

class BasketService
{
    protected Collection $products;
    protected Collection $deliveryRules;
    protected Collection $offers;
    protected Collection $items;
    protected ?Collection $productCache = null;

    /**
     * Initialize the basket with product catalogue, delivery rules, and offers
     *
     * @param Collection|array $products Product catalogue
     * @param array $deliveryRules Delivery charge rules [threshold => cost]
     * @param array<OfferInterface> $offers Special offers
     */
    public function __construct(
        Collection|array $products,
        array $deliveryRules = [],
        array $offers = []
    ) {
        $this->products = $products instanceof Collection ? $products : collect($products);
        $this->deliveryRules = collect($deliveryRules)->sortKeysDesc();
        $this->offers = collect($offers);
        $this->items = collect();
        
        // Cache products by code for O(1) lookup
        $this->productCache = $this->products->keyBy(fn($p) => 
            is_array($p) ? $p['code'] : $p->code
        );
    }

    /**
     * Add a product to the basket by product code
     *
     * @param string $productCode
     * @return $this
     * @throws ProductNotFoundException
     */
    public function add(string $productCode): self
    {
        $product = $this->productCache->get($productCode);

        if (!$product) {
            throw new ProductNotFoundException($productCode);
        }

        $this->items->push($product);
        
        return $this;
    }

    /**
     * Calculate the total cost including delivery and offers
     *
     * @return float
     */
    public function total(): float
    {
        if ($this->items->isEmpty()) {
            return 0.0;
        }

        $subtotal = $this->calculateSubtotal();
        $discount = $this->calculateDiscount();
        $discountedSubtotal = $subtotal - $discount;
        $delivery = $this->calculateDelivery($discountedSubtotal);

        return round($discountedSubtotal + $delivery, 2, PHP_ROUND_HALF_DOWN);
    }

    /**
     * Calculate the subtotal of all items at full price
     *
     * @return float
     */
    protected function calculateSubtotal(): float
    {
        return $this->items->sum(fn($product) => 
            is_array($product) ? $product['price'] : $product->price
        );
    }

    /**
     * Calculate discount based on special offers
     *
     * @return float
     */
    protected function calculateDiscount(): float
    {
        return $this->offers->sum(fn(OfferInterface $offer) => 
            $offer->calculateDiscount($this->items->all())
        );
    }

    /**
     * Calculate delivery charge based on subtotal
     *
     * @param float $subtotal
     * @return float
     */
    protected function calculateDelivery(float $subtotal): float
    {
        foreach ($this->deliveryRules as $threshold => $cost) {
            if ($subtotal >= $threshold) {
                return (float) $cost;
            }
        }

        return (float) $this->deliveryRules->last();
    }

    /**
     * Get all items in the basket
     *
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Get the number of items in the basket
     *
     * @return int
     */
    public function count(): int
    {
        return $this->items->count();
    }

    /**
     * Check if basket is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * Clear all items from the basket
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->items = collect();
        
        return $this;
    }

    /**
     * Get breakdown of costs
     *
     * @return array
     */
    public function getBreakdown(): array
    {
        $subtotal = $this->calculateSubtotal();
        $discount = $this->calculateDiscount();
        $discountedSubtotal = $subtotal - $discount;
        $delivery = $this->calculateDelivery($discountedSubtotal);
        $total = round($discountedSubtotal + $delivery, 2, PHP_ROUND_HALF_DOWN);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => round($discount, 2),
            'discounted_subtotal' => round($discountedSubtotal, 2),
            'delivery' => round($delivery, 2),
            'total' => $total,
        ];
    }
}
