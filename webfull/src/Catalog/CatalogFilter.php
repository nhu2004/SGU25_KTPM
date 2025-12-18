<?php
declare(strict_types=1);

namespace App\Catalog;

final class CatalogFilter
{
    public const DEFAULT_MIN = 0;
    public const DEFAULT_MAX = 15000000;
    public const DEFAULT_PER_PAGE = 9;

    /** @return array{page:int, begin:int} */
    public function normalizePage(?int $pagenumber, int $perPage = self::DEFAULT_PER_PAGE): array
    {
        $page = (int)($pagenumber ?? 1);
        $page = max(1, $page);
        $begin = ($page - 1) * $perPage;
        return ['page' => $page, 'begin' => $begin];
    }

    /** @return array{from:int,to:int,show_tag:bool} */
    public function normalizePriceRange(?int $from, ?int $to, int $min = self::DEFAULT_MIN, int $max = self::DEFAULT_MAX): array
    {
        $price_from = (int)($from ?? $min);
        $price_to   = (int)($to   ?? $max);

        $price_from = max($min, min($max, $price_from));
        $price_to   = max($min, min($max, $price_to));

        if ($price_from > $price_to) {
            [$price_from, $price_to] = [$price_to, $price_from];
        }

        $show = !($price_from === $min && $price_to === $max);
        return ['from' => $price_from, 'to' => $price_to, 'show_tag' => $show];
    }

    /** @return string ''|'asc'|'desc' */
    public function normalizePriceSort(?string $pricesort): string
    {
        $v = strtolower(trim((string)$pricesort));
        return in_array($v, ['asc', 'desc'], true) ? $v : '';
    }
}
