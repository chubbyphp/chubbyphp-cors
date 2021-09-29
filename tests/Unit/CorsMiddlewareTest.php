<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers \Chubbyphp\Cors\CorsMiddleware
 *
 * @internal
 */
final class CorsMiddlewareTest extends TestCase
{
    use MockByCallsTrait;

    public function testPreflightWithoutOrigin(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('options'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testPreflightWithOrigin(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('options'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(false),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testPreflightWithOriginAndMethod(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('options'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
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

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testPreflightWithOriginAndMethodAndHeaders(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('options'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'Accept, Content-Type')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '600')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedMethods')->with()->willReturn(['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedHeaders')->with()->willReturn(['Accept', 'Content-Type']),
        ]);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testPreflightWithOriginAndCredentialsAndExposeHeadersAndMethodAndHeaders(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('options'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'true')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', 'X-C-1, X-C-2')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'Accept, Content-Type')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '7200')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedMethods')->with()->willReturn(['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(true),
            Call::create('getAllowedHeaders')->with()->willReturn(['Accept', 'Content-Type']),
        ]);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            ['X-C-1', 'X-C-2'],
            true,
            7200
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testWithoutOrigin(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('get'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn(null),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testWithOrigin(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('get'),
        ]);

        /** @var MockObject|ResponseInterface $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }

    public function testWithOriginAndCredentialsAndExposeHeaders(): void
    {
        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getMethod')->with()->willReturn('get'),
        ]);

        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://mydomain.tld')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'true')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Expose-Headers', 'X-C-1, X-C-2')->willReturnSelf(),
        ]);

        /** @var MockObject|RequestHandlerInterface $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        /** @var MockObject|ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var MockObject|OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class, [
            Call::create('negotiate')->with($request)->willReturn('https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface|MockObject $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface|MockObject $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            ['X-C-1', 'X-C-2'],
            true
        );

        self::assertSame($response, $middleware->process($request, $requestHandler));
    }
}
