<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation;

use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\HeadersNegotiator
 */
final class HeadersNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn(false),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testWithSameHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn(true),
            Call::create('getHeaderLine')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn('Accept, Authorization, Content-Type'),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithSameHeadersLowerCase(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn(true),
            Call::create('getHeaderLine')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn('accept, Authorization, Content-Type, x-custom'),
        ]);

        $negotiator = new HeadersNegotiator(['x-custom', 'authorization', 'Accept', 'content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithLessHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn(true),
            Call::create('getHeaderLine')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn('Authorization, Accept'),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithToManyHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('hasHeader')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn(true),
            Call::create('getHeaderLine')
                ->with(HeadersNegotiator::HEADER)
                ->willReturn('accept, Content-Type, Authorization'),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testGetAllowedHeaders(): void
    {
        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertSame(['Authorization', 'Accept', 'Content-Type'], $negotiator->getAllowedHeaders());
    }
}
