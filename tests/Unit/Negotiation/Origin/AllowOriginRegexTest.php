<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation\Origin;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex
 */
final class AllowOriginRegexTest extends TestCase
{
    public function testDoesMatch(): void
    {
        $allowOrigin = new AllowOriginRegex('^https\://my');

        self::assertTrue($allowOrigin->match('https://mydomain.tld'));
    }

    public function testDoesNotMatch(): void
    {
        $allowOrigin = new AllowOriginRegex('^ttps\://my');

        self::assertFalse($allowOrigin->match('https://mydomain.tld'));
    }
}
