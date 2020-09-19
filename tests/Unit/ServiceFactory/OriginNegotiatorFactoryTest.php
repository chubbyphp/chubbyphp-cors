<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Cors\ServiceFactory\OriginNegotiatorFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\OriginNegotiatorFactory
 *
 * @internal
 */
final class OriginNegotiatorFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvoke(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
        $allowOriginsReflectionProperty->setAccessible(true);

        self::assertEquals([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://myproject\.'),
        ], $allowOriginsReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
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
        $allowOriginsReflectionProperty->setAccessible(true);

        self::assertEquals([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://myproject\.'),
        ], $allowOriginsReflectionProperty->getValue($service));
    }
}
