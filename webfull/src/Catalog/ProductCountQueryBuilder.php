<?php
declare(strict_types=1);

namespace App\Catalog;

final class ProductCountQueryBuilder
{
    /**
     * Build COUNT query theo đúng logic product-sort.php (đếm sản phẩm theo filter).
     *
     * @return array{sql:string, params:array<int, int>}
     */
    public function build(?int $priceFrom, ?int $priceTo, ?int $categoryId, ?int $brandId): array
    {
        $where = " WHERE product_status = 1";
        $params = [];

        // ưu tiên category trước giống code gốc
        if ($categoryId && $categoryId > 0) {
            $where .= " AND product_category = ?";
            $params[] = $categoryId;
        } elseif ($brandId && $brandId > 0) {
            $where .= " AND product_brand = ?";
            $params[] = $brandId;
        }

        // filter price (normalize về BETWEEN)
        if ($priceFrom !== null && $priceTo !== null) {
            $pf = (int)$priceFrom;
            $pt = (int)$priceTo;
            if ($pf > $pt) { [$pf, $pt] = [$pt, $pf]; }

            $where .= " AND product_price BETWEEN ? AND ?";
            $params[] = $pf;
            $params[] = $pt;
        }

        $sql = "SELECT COUNT(*) AS cnt FROM product" . $where;
        return ['sql' => $sql, 'params' => $params];
    }
}
