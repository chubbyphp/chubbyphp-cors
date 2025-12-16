<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\ServiceFactory\HeadersNegotiatorFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\HeadersNegotiatorFactory
 *
 * @internal
 */
final class HeadersNegotiatorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [
                        'allowHeaders' => ['Accept', 'Content-Type'],
                    ],
                ],
            ]),
        ]);

        $factory = new HeadersNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(HeadersNegotiator::class, $service);

        $allowHeadersReflectionProperty = new \ReflectionProperty($service, 'allowHeaders');

        self::assertSame(['Accept', 'Content-Type'], $allowHeadersReflectionProperty->getValue($service));
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
                            'allowHeaders' => ['Accept', 'Content-Type'],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [HeadersNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(HeadersNegotiator::class, $service);

        $allowHeadersReflectionProperty = new \ReflectionProperty($service, 'allowHeaders');

        self::assertSame(['Accept', 'Content-Type'], $allowHeadersReflectionProperty->getValue($service));
    }
}
