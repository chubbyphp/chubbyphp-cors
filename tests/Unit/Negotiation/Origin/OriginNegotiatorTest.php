<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation\Origin;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator
 *
 * @internal
 */
final class OriginNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithMissingOrEmptyOrigin(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn(''),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testDoesMatch(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn('https://myproject.com'),
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn('https://otherproject.ch'),
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn('https://otherproject.com'),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertSame('https://myproject.com', $negotiator->negotiate($request));
        self::assertSame('https://otherproject.ch', $negotiator->negotiate($request));
        self::assertSame('https://otherproject.com', $negotiator->negotiate($request));
    }

    public function testNotDoesMatch(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn('https://myproject.ch'),
            Call::create('getHeaderLine')->with(OriginNegotiator::HEADER)->willReturn('ttps://otherproject.ch'),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertNull($negotiator->negotiate($request));
        self::assertNull($negotiator->negotiate($request));
    }
}
