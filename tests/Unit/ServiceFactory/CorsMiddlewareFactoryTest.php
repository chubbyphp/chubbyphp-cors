<?php

declare(strict_types=1);

namespace Chubbyphp\Tests\Cors\Unit\ServiceFactory;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Cors\ServiceFactory\CorsMiddlewareFactory;
use Chubbyphp\Mock\MockMethod\WithReturn;
use Chubbyphp\Mock\MockObjectBuilder;
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
    public function testInvokeWithDefaults(): void
    {
        $builder = new MockObjectBuilder();

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, []);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [],
                ],
            ]),
            new WithReturn('has', [OriginNegotiatorInterface::class], true),
            new WithReturn('get', [OriginNegotiatorInterface::class], $originNegotiator),
            new WithReturn('has', [MethodNegotiatorInterface::class], true),
            new WithReturn('get', [MethodNegotiatorInterface::class], $methodNegotiator),
            new WithReturn('has', [HeadersNegotiatorInterface::class], true),
            new WithReturn('get', [HeadersNegotiatorInterface::class], $headersNegotiator),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
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
        $builder = new MockObjectBuilder();

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, []);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
                'chubbyphp' => [
                    'cors' => [
                        'exposeHeaders' => ['Authorization'],
                        'allowCredentials' => true,
                        'maxAge' => 60,
                    ],
                ],
            ]),
            new WithReturn('has', [OriginNegotiatorInterface::class], true),
            new WithReturn('get', [OriginNegotiatorInterface::class], $originNegotiator),
            new WithReturn('has', [MethodNegotiatorInterface::class], true),
            new WithReturn('get', [MethodNegotiatorInterface::class], $methodNegotiator),
            new WithReturn('has', [HeadersNegotiatorInterface::class], true),
            new WithReturn('get', [HeadersNegotiatorInterface::class], $headersNegotiator),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
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
        $builder = new MockObjectBuilder();

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $builder->create(OriginNegotiatorInterface::class, []);

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $builder->create(MethodNegotiatorInterface::class, []);

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $builder->create(HeadersNegotiatorInterface::class, []);

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $builder->create(ResponseFactoryInterface::class, []);

        /** @var ContainerInterface $container */
        $container = $builder->create(ContainerInterface::class, [
            new WithReturn('get', ['config'], [
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
            new WithReturn('has', [OriginNegotiatorInterface::class.'default'], true),
            new WithReturn('get', [OriginNegotiatorInterface::class.'default'], $originNegotiator),
            new WithReturn('has', [MethodNegotiatorInterface::class.'default'], true),
            new WithReturn('get', [MethodNegotiatorInterface::class.'default'], $methodNegotiator),
            new WithReturn('has', [HeadersNegotiatorInterface::class.'default'], true),
            new WithReturn('get', [HeadersNegotiatorInterface::class.'default'], $headersNegotiator),
            new WithReturn('get', [ResponseFactoryInterface::class], $responseFactory),
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
