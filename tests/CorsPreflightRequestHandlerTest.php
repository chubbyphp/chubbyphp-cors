<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors;

use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\MockObject\MockObject;
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

    public function testHandle(): void
    {
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockByCalls(ServerRequestInterface::class);

        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockByCalls(ResponseInterface::class, [
            Call::create('withHeader')->with('Access-Control-Allow-Methods', 'GET, POST')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Allow-Headers', 'Accept, Content-Type')->willReturnSelf(),
            Call::create('withHeader')->with('Access-Control-Max-Age', '600')->willReturnSelf(),
        ]);

        /** @var ResponseFactoryInterface|MockObject $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class, [
            Call::create('createResponse')->with(204, '')->willReturn($response),
        ]);

        $corsPreflightRequestHandler = new CorsPreflightRequestHandler(
            $responseFactory,
            ['GET', 'POST'],
            ['Accept', 'Content-Type']
        );

        self::assertSame($response, $corsPreflightRequestHandler->handle($request));
    }
}
