<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation;

use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\HeadersNegotiator
 *
 * @internal
 */
final class HeadersNegotiatorTest extends TestCase
{
    public function testWithHeaders(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', [HeadersNegotiator::HEADER], false),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testWithSameHeaders(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', [HeadersNegotiator::HEADER], true),
            new WithReturn('getHeaderLine', [HeadersNegotiator::HEADER], 'Accept, Authorization, Content-Type'),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithSameHeadersLowerCase(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', [HeadersNegotiator::HEADER], true),
            new WithReturn(
                'getHeaderLine',
                [HeadersNegotiator::HEADER],
                'accept, Authorization, Content-Type, x-custom'
            ),
        ]);

        $negotiator = new HeadersNegotiator(['x-custom', 'authorization', 'Accept', 'content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithLessHeaders(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', [HeadersNegotiator::HEADER], true),
            new WithReturn('getHeaderLine', [HeadersNegotiator::HEADER], 'Authorization, Accept'),
        ]);

        $negotiator = new HeadersNegotiator(['Authorization', 'Accept', 'Content-Type']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithToManyHeaders(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('hasHeader', [HeadersNegotiator::HEADER], true),
            new WithReturn('getHeaderLine', [HeadersNegotiator::HEADER], 'accept, Content-Type, Authorization'),
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
