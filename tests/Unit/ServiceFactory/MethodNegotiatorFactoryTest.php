<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\ServiceFactory\MethodNegotiatorFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\MethodNegotiatorFactory
 *
 * @internal
 */
final class MethodNegotiatorFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $builder = new MockObjectBuilder();

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [
                        'allowMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
                    ],
                ],
            ]),
        ]);

        $factory = new MethodNegotiatorFactory();

        $service = $factory($container);

        self::assertInstanceOf(MethodNegotiator::class, $service);

        $allowMethodsReflectionProperty = new \ReflectionProperty($service, 'allowMethods');
        $allowMethodsReflectionProperty->setAccessible(true);

        self::assertSame(['DELETE', 'GET', 'POST', 'PUT'], $allowMethodsReflectionProperty->getValue($service));
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
                            'allowMethods' => ['DELETE', 'GET', 'POST', 'PUT'],
                        ],
                    ],
                ],
            ]),
        ]);

        $factory = [MethodNegotiatorFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(MethodNegotiator::class, $service);

        $allowMethodsReflectionProperty = new \ReflectionProperty($service, 'allowMethods');
        $allowMethodsReflectionProperty->setAccessible(true);

        self::assertSame(['DELETE', 'GET', 'POST', 'PUT'], $allowMethodsReflectionProperty->getValue($service));
    }
}
