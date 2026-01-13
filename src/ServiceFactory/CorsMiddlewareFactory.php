<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\ServiceFactory;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

final class CorsMiddlewareFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): CorsMiddleware
    {
        /** @var array{chubbyphp?: array{cors?: array<string, mixed>}} $config */
        $config = $container->get('config');

        /** @var array{exposeHeaders?: array<string>, allowCredentials?: bool, maxAge?: int} $corsConfig */
        $corsConfig = $this->resolveConfig($config['chubbyphp']['cors'] ?? []);

        $exposeHeaders = $corsConfig['exposeHeaders'] ?? [];
        $allowCredentials = $corsConfig['allowCredentials'] ?? false;
        $maxAge = $corsConfig['maxAge'] ?? 600;

        /** @var OriginNegotiatorInterface $originNegotiator */
        $originNegotiator = $this->resolveDependency(
            $container,
            OriginNegotiatorInterface::class,
            OriginNegotiatorFactory::class
        );

        /** @var MethodNegotiatorInterface $methodNegotiator */
        $methodNegotiator = $this->resolveDependency(
            $container,
            MethodNegotiatorInterface::class,
            MethodNegotiatorFactory::class
        );

        /** @var HeadersNegotiatorInterface $headersNegotiator */
        $headersNegotiator = $this->resolveDependency(
            $container,
            HeadersNegotiatorInterface::class,
            HeadersNegotiatorFactory::class
        );

        /** @var ResponseFactoryInterface $responseFactory */
        $responseFactory = $container->get(ResponseFactoryInterface::class);

        return new CorsMiddleware(
            $responseFactory,
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            $exposeHeaders,
            $allowCredentials,
            $maxAge
        );
    }
}
