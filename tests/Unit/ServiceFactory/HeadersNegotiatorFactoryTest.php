<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\ServiceFactory\HeadersNegotiatorFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\HeadersNegotiatorFactory
 *
 * @internal
 */
final class HeadersNegotiatorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
        $allowHeadersReflectionProperty->setAccessible(true);

        self::assertSame(['Accept', 'Content-Type'], $allowHeadersReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
        $allowHeadersReflectionProperty->setAccessible(true);

        self::assertSame(['Accept', 'Content-Type'], $allowHeadersReflectionProperty->getValue($service));
    }
}
