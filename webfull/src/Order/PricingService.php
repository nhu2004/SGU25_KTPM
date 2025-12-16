<?php
declare(strict_types=1);

namespace App\Order;

final class PricingService
{
    /**
     * Tính giá sau sale (%). Sale <0 coi như 0, sale >100 clamp về 100.
     */
    public function priceAfterSale(float $price, float $salePercent): float
    {
        if ($price < 0) $price = 0;
        if ($salePercent < 0) $salePercent = 0;
        if ($salePercent > 100) $salePercent = 100;

        return $price - ($price * $salePercent / 100.0);
    }

    /**
     * Tính total từ cart session item (giống cách bạn đang làm ở cart.php)
     * Mỗi item cần: product_price, product_sale, product_quantity
     */
    public function cartTotal(array $cartItems): float
    {
        $total = 0.0;
        foreach ($cartItems as $item) {
            $priceOld = (float)($item['product_price'] ?? 0);
            $sale     = (float)($item['product_sale'] ?? 0);
            $qty      = (int)($item['product_quantity'] ?? 0);
            if ($qty <= 0) continue;

            $priceNew = $this->priceAfterSale($priceOld, $sale);
            $total += $priceNew * $qty;
        }
        return $total;
    }

    /**
     * Check một line có hợp lệ tồn kho không (giống validate trong cart.php).
     */
    public function isQtyWithinStock(int $qtyInCart, int $stockQty): bool
    {
        if ($qtyInCart <= 0) return false;
        if ($stockQty < 0) $stockQty = 0;
        return $stockQty >= $qtyInCart;
    }
}
