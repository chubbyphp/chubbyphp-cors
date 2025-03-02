<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockMethod\WithReturnSelf;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testPreflightWithoutOrigin(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'options'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], null),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'options'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'false']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], false),
        ]);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'options'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'false']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Methods', 'GET, POST']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], true),
            new WithReturn('getAllowedMethods', [], ['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], false),
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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'options'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'false']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Methods', 'GET, POST']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Headers', 'Accept, Content-Type']),
            new WithReturnSelf('withHeader', ['Access-Control-Max-Age', '600']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], true),
            new WithReturn('getAllowedMethods', [], ['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], true),
            new WithReturn('getAllowedHeaders', [], ['Accept', 'Content-Type']),
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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'options'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'true']),
            new WithReturnSelf('withHeader', ['Access-Control-Expose-Headers', 'X-C-1, X-C-2']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Methods', 'GET, POST']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Headers', 'Accept, Content-Type']),
            new WithReturnSelf('withHeader', ['Access-Control-Max-Age', '7200']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, [
            new WithReturn('createResponse', [204, ''], $response),
        ]);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], true),
            new WithReturn('getAllowedMethods', [], ['GET', 'POST']),
        ]);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], true),
            new WithReturn('getAllowedHeaders', [], ['Accept', 'Content-Type']),
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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'get'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, []);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], null),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'get'),
        ]);

        /** @var ResponseInterface $response */
        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'false']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

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
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getMethod', [], 'get'),
        ]);

        $response = $builder->create(ResponseInterface::class, [
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Origin', 'https://mydomain.tld']),
            new WithReturnSelf('withHeader', ['Access-Control-Allow-Credentials', 'true']),
            new WithReturnSelf('withHeader', ['Access-Control-Expose-Headers', 'X-C-1, X-C-2']),
        ]);

        /** @var RequestHandlerInterface $requestHandler */
        $requestHandler = $builder->create(RequestHandlerInterface::class, [
            new WithReturn('handle', [$request], $response),
        ]);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, [
            new WithReturn('negotiate', [$request], 'https://mydomain.tld'),
        ]);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

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
