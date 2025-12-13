<?php
use PHPUnit\Framework\TestCase;

final class SmokeTest extends TestCase
{
    public function test_ci_pipeline_ok(): void
    {
        $this->assertTrue(true);
    }
}
