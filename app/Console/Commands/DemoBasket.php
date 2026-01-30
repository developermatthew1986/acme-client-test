<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\BasketService;
use Illuminate\Console\Command;

class DemoBasket extends Command
{
    protected $signature = 'basket:demo';
    protected $description = 'Demonstrate the basket system with example calculations';
    
    // Define delivery rules
    protected $deliveryRules = [
        90 => 0.00,    // Orders $90+ get free delivery
        50 => 2.95,    // Orders $50-$89.99 get $2.95 delivery
        0 => 4.95,     // Orders under $50 get $4.95 delivery
    ];

    // Define special offers
    protected $offers = [];

    protected $expectedTotals = [37.85,54.37];

    public function handle()
    {
        $this->info('=== Running Tests ===');
        $this->newLine();

        // Get products from database
        $products = Product::all();
        $this->offers[] = new \App\Services\Offers\BuyOneGetSecondHalfPriceOffer('R01');

        // Test cases
        $testCases = [
            ['B01', 'G01'],
            ['R01', 'R01'],
        ];

        

        foreach ($testCases as $index => $items) {
            $this->calculate($products,$items,$this->expectedTotals[$index]);
        }                
    }

    protected function calculate($products,$items,$expected) {
        $basket = new BasketService($products, $this->deliveryRules, $this->offers);

        $this->info("Test Case " . implode(', ', $items));
        $this->line("--------------------------------------------------");
        $this->newLine();
        foreach ($items as $code) {
            $basket->add($code);
            $product = $products->firstWhere('code', $code);
            $this->line("  + {$product->name} ({$code}): \${$product->price}");
        }

        $total = $basket->total();
        $breakdown = $basket->getBreakdown();
        $this->newLine();
        $this->line("  Sub Total: \${$breakdown['subtotal']}");
        $this->newLine();
        if ($breakdown['discount'] > 0) {
            $this->line("  Discount: -\${$breakdown['discount']}");
        }
        $this->newLine();
        $this->line("  Delivery: \${$breakdown['delivery']}");
        $this->newLine();
        $this->line("  Expected Result : \${$expected}");
        $this->newLine();
        $this->line("  Actual Result:   \${$total}");
        $this->newLine();
        if ($total == $expected) {
            $this->info("  ✓ PASS");
        } else {
            $this->error("  ✗ FAIL");
        }

        $this->newLine();
    }
}
