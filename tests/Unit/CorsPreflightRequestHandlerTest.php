<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit;

use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\CorsPreflightRequestHandler
 */
final class CorsPreflightRequestHandlerTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutOrigin(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn(null),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testWithOriginWithoutMatchingMethod(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn('https:://somehost.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(false),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testWithOriginWithMatchingMethodWithoutMatchingHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn('https:://somehost.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedMethods')->with()->willReturn(['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(false),
        ]);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testWithOriginWithMatchingMethodWithMatchingHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn('https:://somehost.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'X-Awe, X-Some')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '600')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedMethods')->with()->willReturn(['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedHeaders')->with()->willReturn(['X-Awe', 'X-Some']),
        ]);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $requestHandler->handle($request));
    }

    public function testWithOriginWithMatchingMethodWithMatchingHeadersAndModifiedMaxAge(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn('https:://somehost.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'X-Awe, X-Some')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '7200')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedMethods')->with()->willReturn(['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedHeaders')->with()->willReturn(['X-Awe', 'X-Some']),
        ]);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator,
            7200
        );

        self::assertSame($response, $requestHandler->handle($request));
    }
}
