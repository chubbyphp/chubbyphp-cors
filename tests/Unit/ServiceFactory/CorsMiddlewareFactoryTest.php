<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\Mock\Call;
use Chubbyphp\Mock\MockByCallsTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * @covers \Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory
 *
 * @internal
 */
final class CorsMiddlewareFactoryTest extends TestCase
{
    use MockByCallsTrait;

    public function testInvokeWithDefaults(): void
    {
        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'chubbyphp' => [
                    'cors' => [],
                ],
            ]),
            Call::create('has')->with(OriginNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(OriginNegotiatorInterface::class)->willReturn($originNegotiator),
            Call::create('has')->with(MethodNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(MethodNegotiatorInterface::class)->willReturn($methodNegotiator),
            Call::create('has')->with(HeadersNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(HeadersNegotiatorInterface::class)->willReturn($headersNegotiator),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = new CorsMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(CorsMiddleware::class, $service);

        $exposeHeadersReflectionProperty = new \ReflectionProperty($service, 'exposeHeaders');
        $exposeHeadersReflectionProperty->setAccessible(true);

        self::assertSame([], $exposeHeadersReflectionProperty->getValue($service));

        $allowCredentialsReflectionProperty = new \ReflectionProperty($service, 'allowCredentials');
        $allowCredentialsReflectionProperty->setAccessible(true);

        self::assertFalse($allowCredentialsReflectionProperty->getValue($service));

        $maxAgeReflectionProperty = new \ReflectionProperty($service, 'maxAge');
        $maxAgeReflectionProperty->setAccessible(true);

        self::assertSame(600, $maxAgeReflectionProperty->getValue($service));
    }

    public function testInvoke(): void
    {
        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'chubbyphp' => [
                    'cors' => [
                        'exposeHeaders' => ['Authorization'],
                        'allowCredentials' => true,
                        'maxAge' => 60,
                    ],
                ],
            ]),
            Call::create('has')->with(OriginNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(OriginNegotiatorInterface::class)->willReturn($originNegotiator),
            Call::create('has')->with(MethodNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(MethodNegotiatorInterface::class)->willReturn($methodNegotiator),
            Call::create('has')->with(HeadersNegotiatorInterface::class)->willReturn(true),
            Call::create('get')->with(HeadersNegotiatorInterface::class)->willReturn($headersNegotiator),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = new CorsMiddlewareFactory();

        $service = $factory($container);

        self::assertInstanceOf(CorsMiddleware::class, $service);

        $exposeHeadersReflectionProperty = new \ReflectionProperty($service, 'exposeHeaders');
        $exposeHeadersReflectionProperty->setAccessible(true);

        self::assertSame(['Authorization'], $exposeHeadersReflectionProperty->getValue($service));

        $allowCredentialsReflectionProperty = new \ReflectionProperty($service, 'allowCredentials');
        $allowCredentialsReflectionProperty->setAccessible(true);

        self::assertTrue($allowCredentialsReflectionProperty->getValue($service));

        $maxAgeReflectionProperty = new \ReflectionProperty($service, 'maxAge');
        $maxAgeReflectionProperty->setAccessible(true);

        self::assertSame(60, $maxAgeReflectionProperty->getValue($service));
    }

    public function testCallStatic(): void
    {
        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->getMockByCalls(OriginNegotiatorInterface::class);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $this->getMockByCalls(MethodNegotiatorInterface::class);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $this->getMockByCalls(HeadersNegotiatorInterface::class);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $this->getMockByCalls(ResponseFactoryInterface::class);

        /** @var ContainerInterface $container */
        $container = $this->getMockByCalls(ContainerInterface::class, [
            Call::create('get')->with('config')->willReturn([
                'chubbyphp' => [
                    'cors' => [
                        'default' => [
                            'exposeHeaders' => ['Authorization'],
                            'allowCredentials' => true,
                            'maxAge' => 60,
                        ],
                    ],
                ],
            ]),
            Call::create('has')->with(OriginNegotiatorInterface::class.'default')->willReturn(true),
            Call::create('get')->with(OriginNegotiatorInterface::class.'default')->willReturn($originNegotiator),
            Call::create('has')->with(MethodNegotiatorInterface::class.'default')->willReturn(true),
            Call::create('get')->with(MethodNegotiatorInterface::class.'default')->willReturn($methodNegotiator),
            Call::create('has')->with(HeadersNegotiatorInterface::class.'default')->willReturn(true),
            Call::create('get')->with(HeadersNegotiatorInterface::class.'default')->willReturn($headersNegotiator),
            Call::create('get')->with(ResponseFactoryInterface::class)->willReturn($responseFactory),
        ]);

        $factory = [CorsMiddlewareFactory::class, 'default'];

        $service = $factory($container);

        self::assertInstanceOf(CorsMiddleware::class, $service);

        self::assertInstanceOf(CorsMiddleware::class, $service);

        $exposeHeadersReflectionProperty = new \ReflectionProperty($service, 'exposeHeaders');
        $exposeHeadersReflectionProperty->setAccessible(true);

        self::assertSame(['Authorization'], $exposeHeadersReflectionProperty->getValue($service));

        $allowCredentialsReflectionProperty = new \ReflectionProperty($service, 'allowCredentials');
        $allowCredentialsReflectionProperty->setAccessible(true);

        self::assertTrue($allowCredentialsReflectionProperty->getValue($service));

        $maxAgeReflectionProperty = new \ReflectionProperty($service, 'maxAge');
        $maxAgeReflectionProperty->setAccessible(true);

        self::assertSame(60, $maxAgeReflectionProperty->getValue($service));
    }
}
