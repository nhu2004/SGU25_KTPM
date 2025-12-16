<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Order\PricingService;

final class PricingServiceTest extends TestCase
{
    public function testPriceAfterSale(): void
    {
        $svc = new PricingService();

        $this->assertSame(90.0, $svc->priceAfterSale(100.0, 10.0));
        $this->assertSame(100.0, $svc->priceAfterSale(100.0, 0.0));
        $this->assertSame(0.0, $svc->priceAfterSale(100.0, 100.0));
    }

    public function testCartTotal(): void
    {
        $svc = new PricingService();

        $cart = [
            ['product_price' => 100, 'product_sale' => 10, 'product_quantity' => 2], // 90*2=180
            ['product_price' => 50,  'product_sale' => 0,  'product_quantity' => 1], // 50
        ];

        $this->assertSame(230.0, $svc->cartTotal($cart));
    }

    public function testIsQtyWithinStock(): void
    {
        $svc = new PricingService();

        $this->assertTrue($svc->isQtyWithinStock(2, 2));
        $this->assertFalse($svc->isQtyWithinStock(3, 2));
        $this->assertFalse($svc->isQtyWithinStock(0, 10));
    }
}
