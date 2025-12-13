<?php
declare(strict_types=1);

namespace App\Cart;

final class CartService
{
    // tăng 1 (nút +). Trả về [cart, status] status=1 success, 0 error (vượt tồn)
    public function increase(array $cart, int $productId, int $stockMax): array
    {
        $status = 1;
        $new = [];

        foreach ($cart as $item) {
            if ((int)$item['product_id'] === $productId) {
                $next = (int)$item['product_quantity'] + 1;
                if ($stockMax > 0 && $next > $stockMax) {
                    $status = 0;
                } else {
                    $item['product_quantity'] = $next;
                }
            }
            $new[] = $item;
        }

        return [$new, $status];
    }

    // giảm 1 (nút -). Nếu xuống 0 thì xóa item
    public function decrease(array $cart, int $productId): array
    {
        $new = [];
        foreach ($cart as $item) {
            if ((int)$item['product_id'] === $productId) {
                $next = (int)$item['product_quantity'] - 1;
                if ($next <= 0) {
                    continue; // xóa
                }
                $item['product_quantity'] = $next;
            }
            $new[] = $item;
        }
        return $new;
    }

    // update qty bằng tay. Trả về [cart, status] status=1 ok, 0 bị clamp về stock
    public function updateQty(array $cart, int $productId, int $qtyInput, int $stockMax): array
    {
        if ($qtyInput < 0) $qtyInput = 0;

        $status = 1;
        $new = [];

        foreach ($cart as $item) {
            if ((int)$item['product_id'] === $productId) {
                if ($qtyInput === 0) {
                    continue; // xóa
                }
                if ($stockMax > 0 && $qtyInput > $stockMax) {
                    $status = 0;
                    $item['product_quantity'] = $stockMax;
                } else {
                    $item['product_quantity'] = $qtyInput;
                }
            }
            $new[] = $item;
        }

        return [$new, $status];
    }

    // add to cart: cộng dồn qty nhưng không vượt tồn
    public function addOrMerge(array $cart, array $product, int $qtyAdd): array
    {
        $productId = (int)$product['product_id'];
        $stockMax  = (int)($product['stock_qty'] ?? 0);
        $qtyAdd    = max(1, $qtyAdd);

        if ($stockMax > 0) {
            $qtyAdd = min($qtyAdd, $stockMax);
        }

        $found = false;
        $new = [];

        foreach ($cart as $item) {
            if ((int)$item['product_id'] === $productId) {
                $found = true;
                $next = (int)$item['product_quantity'] + $qtyAdd;
                if ($stockMax > 0) $next = min($next, $stockMax);
                $item['product_quantity'] = max(1, $next);
            }
            $new[] = $item;
        }

        if (!$found) {
            $new[] = [
                'product_id' => $productId,
                'product_quantity' => $qtyAdd,
                'product_price' => (int)($product['product_price'] ?? 0),
            ];
        }

        return $new;
    }

    public function total(array $cart): int
    {
        $sum = 0;
        foreach ($cart as $item) {
            $sum += (int)$item['product_price'] * (int)$item['product_quantity'];
        }
        return $sum;
    }
}
