<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation\Origin;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact
 */
final class AllowOriginExactTest extends TestCase
{
    public function testDoesMatch(): void
    {
        $allowOrigin = new AllowOriginExact('https://mydomain.tld');

        self::assertTrue($allowOrigin->match('https://mydomain.tld'));
    }

    public function testDoesNotMatch(): void
    {
        $allowOrigin = new AllowOriginExact('ttps://mydomain.tld');

        self::assertFalse($allowOrigin->match('https://mydomain.tld'));
    }
}
