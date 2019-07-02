<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation\Origin;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginSame;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Cors\Negotiation\Origin\AllowOriginSame
 */
final class AllowOriginSameTest extends TestCase
{
    public function testDoesMatch(): void
    {
        $allowOrigin = new AllowOriginSame('https://mydomain.tld');

        self::assertTrue($allowOrigin->match('https://mydomain.tld'));
    }

    public function testDoesNotMatch(): void
    {
        $allowOrigin = new AllowOriginSame('ttps://mydomain.tld');

        self::assertFalse($allowOrigin->match('https://mydomain.tld'));
    }
}
