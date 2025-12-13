<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Cart\CartService;

final class CartServiceTest extends TestCase
{
    public function testIncreaseDoesNotExceedStock(): void
    {
        $svc = new CartService();
        $cart = [['product_id' => 1, 'product_quantity' => 5, 'product_price' => 100]];

        [$new, $status] = $svc->increase($cart, 1, 5);

        $this->assertSame(0, $status);
        $this->assertSame(5, $new[0]['product_quantity']);
    }

    public function testDecreaseToZeroRemovesItem(): void
    {
        $svc = new CartService();
        $cart = [['product_id' => 1, 'product_quantity' => 1, 'product_price' => 100]];

        $new = $svc->decrease($cart, 1);

        $this->assertCount(0, $new);
    }

    public function testUpdateQtyClampToStock(): void
    {
        $svc = new CartService();
        $cart = [['product_id' => 1, 'product_quantity' => 1, 'product_price' => 100]];

        [$new, $status] = $svc->updateQty($cart, 1, 999, 10);

        $this->assertSame(0, $status);
        $this->assertSame(10, $new[0]['product_quantity']);
    }

    public function testAddOrMerge(): void
    {
        $svc = new CartService();
        $cart = [['product_id' => 1, 'product_quantity' => 2, 'product_price' => 100]];

        $cart = $svc->addOrMerge($cart, ['product_id' => 1, 'product_price' => 100, 'stock_qty' => 10], 3);

        $this->assertSame(5, $cart[0]['product_quantity']);
    }

    public function testTotal(): void
    {
        $svc = new CartService();
        $cart = [
            ['product_id' => 1, 'product_quantity' => 2, 'product_price' => 100],
            ['product_id' => 2, 'product_quantity' => 1, 'product_price' => 50],
        ];

        $this->assertSame(250, $svc->total($cart));
    }
}
