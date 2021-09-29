<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation;

use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\MethodNegotiator
 *
 * @internal
 */
final class MethodNegotiatorTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithEmptyMethod(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')
                ->with(MethodNegotiator::HEADER)
                ->willReturn(''),
        ]);

        $negotiator = new MethodNegotiator(['GET', 'POST']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testWithAllowedMethod(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')
                ->with(MethodNegotiator::HEADER)
                ->willReturn('POST'),
        ]);

        $negotiator = new MethodNegotiator(['GET', 'POST']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithNotAllowedMethod(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')
                ->with(MethodNegotiator::HEADER)
                ->willReturn('PUT'),
        ]);

        $negotiator = new MethodNegotiator(['GET', 'POST']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testGetAllowedMethods(): void
    {
        $negotiator = new MethodNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertSame(['Authorization', 'Accept', 'Content-Type'], $negotiator->getAllowedMethods());
    }
}
