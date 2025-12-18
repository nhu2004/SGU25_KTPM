<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Catalog\CatalogFilter;

final class CatalogFilterTest extends TestCase
{
    public function testNormalizePageDefault(): void
    {
        $f = new CatalogFilter();

        $r = $f->normalizePage(null);
        $this->assertSame(1, $r['page']);
        $this->assertSame(0, $r['begin']);
    }

    public function testNormalizePageClampsToMin1(): void
    {
        $f = new CatalogFilter();

        $r = $f->normalizePage(0);
        $this->assertSame(1, $r['page']);
        $this->assertSame(0, $r['begin']);
    }

    public function testNormalizePageBeginCalculation(): void
    {
        $f = new CatalogFilter();

        $r = $f->normalizePage(3);
        $this->assertSame(3, $r['page']);
        $this->assertSame(18, $r['begin']); // (3-1)*9
    }

    public function testNormalizePriceRangeDefault(): void
    {
        $f = new CatalogFilter();

        $r = $f->normalizePriceRange(null, null);
        $this->assertSame(0, $r['from']);
        $this->assertSame(15000000, $r['to']);
        $this->assertFalse($r['show_tag']);
    }

    public function testNormalizePriceRangeClampAndSwap(): void
    {
        $f = new CatalogFilter();

        $r = $f->normalizePriceRange(-10, 1000);
        $this->assertSame(0, $r['from']);
        $this->assertSame(1000, $r['to']);
        $this->assertTrue($r['show_tag']);

        $r = $f->normalizePriceRange(100, 999999999);
        $this->assertSame(100, $r['from']);
        $this->assertSame(15000000, $r['to']);
        $this->assertTrue($r['show_tag']);

        $r = $f->normalizePriceRange(9000, 2000);
        $this->assertSame(2000, $r['from']);
        $this->assertSame(9000, $r['to']);
        $this->assertTrue($r['show_tag']);
    }

    public function testNormalizePriceSort(): void
    {
        $f = new CatalogFilter();

        $this->assertSame('asc', $f->normalizePriceSort('asc'));
        $this->assertSame('desc', $f->normalizePriceSort('DESC'));
        $this->assertSame('', $f->normalizePriceSort('hack'));
        $this->assertSame('', $f->normalizePriceSort(null));
    }
}
