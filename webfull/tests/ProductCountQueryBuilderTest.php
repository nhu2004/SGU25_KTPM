<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Catalog\ProductCountQueryBuilder;

final class ProductCountQueryBuilderTest extends TestCase
{
    public function testBuildDefault(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(null, null, null, null);

        $this->assertStringContainsString("WHERE product_status = 1", $r['sql']);
        $this->assertSame([], $r['params']);
    }

    public function testBuildCategoryOnly(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(null, null, 3, null);

        $this->assertStringContainsString("product_category = ?", $r['sql']);
        $this->assertSame([3], $r['params']);
    }

    public function testBuildBrandOnly(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(null, null, null, 9);

        $this->assertStringContainsString("product_brand = ?", $r['sql']);
        $this->assertSame([9], $r['params']);
    }

    public function testCategoryHasPriorityOverBrand(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(null, null, 2, 99);

        $this->assertStringContainsString("product_category = ?", $r['sql']);
        $this->assertStringNotContainsString("product_brand = ?", $r['sql']);
        $this->assertSame([2], $r['params']);
    }

    public function testBuildWithPriceRange(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(1000, 5000, null, null);

        $this->assertStringContainsString("product_price BETWEEN ? AND ?", $r['sql']);
        $this->assertSame([1000, 5000], $r['params']);
    }

    public function testBuildWithPriceSwapWhenFromGreaterThanTo(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(9000, 2000, null, null);

        $this->assertSame([2000, 9000], $r['params']);
    }

    public function testBuildCategoryAndPrice(): void
    {
        $b = new ProductCountQueryBuilder();
        $r = $b->build(100, 200, 5, null);

        $this->assertStringContainsString("product_category = ?", $r['sql']);
        $this->assertStringContainsString("product_price BETWEEN ? AND ?", $r['sql']);
        $this->assertSame([5, 100, 200], $r['params']);
    }
}
