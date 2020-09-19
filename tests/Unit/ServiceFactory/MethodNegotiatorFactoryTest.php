<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\ServiceFactory\MethodNegotiatorFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\MethodNegotiatorFactory
 *
 * @internal
 */
final class MethodNegotiatorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
