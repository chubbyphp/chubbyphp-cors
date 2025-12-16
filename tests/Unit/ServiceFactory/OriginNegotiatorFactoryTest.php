<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Cors\ServiceFactory\OriginNegotiatorFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\OriginNegotiatorFactory
 *
 * @internal
 */
final class OriginNegotiatorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [
                        'allowOrigins' => [
                            'https://myproject.com' => AllowOriginExact::class,
                            '^https://myproject\.' => AllowOriginRegex::class,
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = new OriginNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(OriginNegotiator::class, $service);

        $allowOriginsReflectionProperty = new \ReflectionProperty($service, 'allowOrigins');

        self::assertEquals([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://myproject\.'),
        ], $allowOriginsReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [
                        'default' => [
                            'allowOrigins' => [
                                'https://myproject.com' => AllowOriginExact::class,
                                '^https://myproject\.' => AllowOriginRegex::class,
                            ],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [OriginNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(OriginNegotiator::class, $service);

        $allowOriginsReflectionProperty = new \ReflectionProperty($service, 'allowOrigins');

        self::assertEquals([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://myproject\.'),
        ], $allowOriginsReflectionProperty->getValue($service));
    }
}
