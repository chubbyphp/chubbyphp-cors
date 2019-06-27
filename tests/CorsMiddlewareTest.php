<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
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

    public function testProcessWithEmptyOrigin(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn(''),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', '')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $CorsMiddleware = new CorsMiddleware(['https://myproject.com']);

        self::assertSame($response, $CorsMiddleware->process($request, $requestHandler));
    }

    public function testProcessWithNotAllowOrigin(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https://otherproject.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', '')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $CorsMiddleware = new CorsMiddleware(['https://myproject.com']);

        self::assertSame($response, $CorsMiddleware->process($request, $requestHandler));
    }

    public function testProcessWithAllowOrigin(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https://myproject.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://myproject.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $CorsMiddleware = new CorsMiddleware(['https://myproject.com']);

        self::assertSame($response, $CorsMiddleware->process($request, $requestHandler));
    }

    public function testProcessWithNotAllowOriginCauseNotMatchinPattern(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https://myproject.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', '')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $CorsMiddleware = new CorsMiddleware(['~^ttps\://my']);

        self::assertSame($response, $CorsMiddleware->process($request, $requestHandler));
    }

    public function testProcessWithAllowOriginCauseMatchinPattern(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class, [
            Call::create('getHeaderLine')->with('Origin')->willReturn('https://myproject.com'),
        ]);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Origin', 'https://myproject.com')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Credentials', 'false')->willReturnSelf(),
        ]);

        /** @var RequestHandlerInterface|MockObject $requestHandler */
        $requestHandler = $this->getMockByCalls(RequestHandlerInterface::class, [
            Call::create('handle')->with($request)->willReturn($response),
        ]);

        $CorsMiddleware = new CorsMiddleware(['~^https\://my']);

        self::assertSame($response, $CorsMiddleware->process($request, $requestHandler));
    }
}
