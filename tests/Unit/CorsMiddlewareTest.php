<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Cors\CorsMiddleware
 */
final class CorsMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testWithoutOrigin(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('allowOrigin', null)->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var OriginNegotiatorInterface|MockObject $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
        ]);

        $middleware = new CorsMiddleware($originNegotiator);

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testWithOriginNotAllowedCredentialsAndNoExposedHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('allowOrigin', 'https:://somehost.com')->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https:://somehost.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', '')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var OriginNegotiatorInterface|MockObject $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https:://somehost.com'),
        ]);

        $middleware = new CorsMiddleware($originNegotiator);

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testWithOriginAllowedCredentialsAndExposedHeaders(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('withAttribute')->with('allowOrigin', 'https:://somehost.com')->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https:://somehost.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'true')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', 'X-Awe, X-Some')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var OriginNegotiatorInterface|MockObject $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https:://somehost.com'),
        ]);

        $middleware = new CorsMiddleware($originNegotiator, true, ['X-Awe', 'X-Some']);

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }
}
