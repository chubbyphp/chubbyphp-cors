<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation;

use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\MethodNegotiator
 *
 * @internal
 */
final class MethodNegotiatorTest extends TestCase
{
    public function testWithEmptyMethod(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [MethodNegotiator::HEADER], ''),
        ]);

        $negotiator = new MethodNegotiator(['GET', 'POST']);

        self::assertFalse($negotiator->negotiate($request));
    }

    public function testWithAllowedMethod(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [MethodNegotiator::HEADER], 'POST'),
        ]);

        $negotiator = new MethodNegotiator(['GET', 'POST']);

        self::assertTrue($negotiator->negotiate($request));
    }

    public function testWithNotAllowedMethod(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [MethodNegotiator::HEADER], 'PUT'),
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
