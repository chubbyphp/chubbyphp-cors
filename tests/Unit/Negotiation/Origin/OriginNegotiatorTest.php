<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\Negotiation\Origin;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @covers \Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator
 *
 * @internal
 */
final class OriginNegotiatorTest extends TestCase
{
    public function testWithMissingOrEmptyOrigin(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], ''),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertNull($negotiator->negotiate($request));
    }

    public function testDoesMatch(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ServerRequestInterface $request */
        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], 'https://myproject.com'),
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], 'https://otherproject.ch'),
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], 'https://otherproject.com'),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertSame('https://myproject.com', $negotiator->negotiate($request));
        self::assertSame('https://otherproject.ch', $negotiator->negotiate($request));
        self::assertSame('https://otherproject.com', $negotiator->negotiate($request));
    }

    public function testNotDoesMatch(): void
    {
        $builder = new MockObjectBuilder();

        $request = $builder->create(ServerRequestInterface::class, [
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], 'https://myproject.ch'),
            new WithReturn('getHeaderLine', [OriginNegotiator::HEADER], 'ttps://otherproject.ch'),
        ]);

        $negotiator = new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://otherproject\.'),
        ]);

        self::assertNull($negotiator->negotiate($request));
        self::assertNull($negotiator->negotiate($request));
    }
}
