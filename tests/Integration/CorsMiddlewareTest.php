<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Integration;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @coversNothing
 *
 * @internal
 */
final class CorsMiddlewareTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testDefault(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', ['Origin'], 'https://somehost.com'),
            new WithReturn('getMethod', [], 'get'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://somehost.com']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'true']),
            new WithReturnSelf('withHeader', ['Access-Control-Expose-Headers', 'X-Custom']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        $originNegotiator = new OriginNegotiator([
            new AllowOriginExact('https://somehost.com'),
        ]);

        $methodNegotiator = new MethodNegotiator(['GET', 'POST']);

        $headersNegotiator = new HeadersNegotiator(['X-Awe', 'X-Some']);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            ['X-Custom'],
            true,
            7200
        );

        $middleware->process($request, $requestHandler);
    }

    #[DoesNotPerformAssertions]
    public function testPreflight(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', ['Origin'], 'https://somehost.com'),
            new WithReturn('getMethod', [], 'options'),
            new WithReturn('getHeaderLine', ['Access-Control-Request-Method'], 'POST'),
            new WithReturn('hasHeader', ['Access-Control-Request-Headers'], true),
            new WithReturn('getHeaderLine', ['Access-Control-Request-Headers'], 'X-Some'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://somehost.com']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'true']),
            new WithReturnSelf('withHeader', ['Access-Control-Expose-Headers', 'X-Custom']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Methods', 'GET, POST']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Headers', 'X-Awe, X-Some']),
            new WithReturnSelf('withHeader', ['Access-Control-Max-Age', '7200']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        $originNegotiator = new OriginNegotiator([
            new AllowOriginExact('https://somehost.com'),
        ]);

        $methodNegotiator = new MethodNegotiator(['GET', 'POST']);

        $headersNegotiator = new HeadersNegotiator(['X-Awe', 'X-Some']);

        $middleware = new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            ['X-Custom'],
            true,
            7200
        );

        $middleware->process($request, $requestHandler);
    }
}
