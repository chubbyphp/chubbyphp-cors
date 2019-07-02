<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Functional;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginSame;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @coversNothing
 */
final class CorsPreflightWithMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testSample(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https:://somehost.com'),
            Call::create('withAttribute')->with('allowOrigin', 'https:://somehost.com')->willReturnSelf(),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https:://somehost.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', '')->willReturnSelf(),
        ]);

        $originNegotiator = new OriginNegotiator([
            new AllowOriginSame('https:://somehost.com'),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $middleware = new CorsMiddleware($originNegotiator);
        $middleware->process($request, $requestHandler);
    }

    public function testPreflight(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https:://somehost.com'),
            Call::create('withAttribute')->with('allowOrigin', 'https:://somehost.com')->willReturnSelf(),
            Call::create('getAttribute')->with('allowOrigin', null)->willReturn('https:://somehost.com'),
            Call::create('getHeaderLine')->with('Access-Control-Request-Method')->willReturn('POST'),
            Call::create('hasHeader')->with('Access-Control-Request-Headers')->willReturn(true),
            Call::create('getHeaderLine')->with('Access-Control-Request-Headers')->willReturn('X-Some'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'X-Awe, X-Some')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '600')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https:://somehost.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', '')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        $originNegotiator = new OriginNegotiator([
            new AllowOriginSame('https:://somehost.com'),
        ]);

        $methodNegotiator = new MethodNegotiator(['GET', 'POST']);

        $headersNegotiator = new HeadersNegotiator(['X-Awe', 'X-Some']);

        $requestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            $methodNegotiator,
            $headersNegotiator
        );

        $middleware = new CorsMiddleware($originNegotiator);
        $middleware->process($request, $requestHandler);
    }
}
