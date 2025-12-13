<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Auth\PasswordVerifier;

final class PasswordVerifierTest extends TestCase
{
    public function testBcryptPasswordHash(): void
    {
        $v = new PasswordVerifier();
        $hash = password_hash('Abc@123', PASSWORD_BCRYPT);

        $this->assertTrue($v->verify('Abc@123', $hash));
        $this->assertFalse($v->verify('wrong', $hash));
    }

    public function testSha1Variants(): void
    {
        $v = new PasswordVerifier();

        $this->assertTrue($v->verify('abc', sha1('abc')));
        $this->assertTrue($v->verify('abc', sha1(md5('abc'))));
        $this->assertTrue($v->verify('ABC', sha1(strtolower('ABC')))); // sha1(lower(pass))
        $this->assertFalse($v->verify('abc', sha1('abcd')));
    }

    public function testMd5Variants(): void
    {
        $v = new PasswordVerifier();

        $this->assertTrue($v->verify('abc', md5('abc')));
        $this->assertTrue($v->verify('ABC', md5(strtolower('ABC'))));
        $this->assertFalse($v->verify('abc', md5('abcd')));
    }

    public function testPlaintext(): void
    {
        $v = new PasswordVerifier();

        $this->assertTrue($v->verify('123456', '123456'));
        $this->assertFalse($v->verify('123456', '654321'));
    }
}
