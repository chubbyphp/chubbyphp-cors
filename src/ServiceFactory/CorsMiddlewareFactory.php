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
        $config = $this->resolveConfig($container->get('config')['chubbyphp']['cors'] ?? []);

        $exposeHeaders = $config['exposeHeaders'] ?? [];
        $allowCredentials = $config['allowCredentials'] ?? false;
        $maxAge = $config['maxAge'] ?? 600;

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

        return new CorsMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $originNegotiator,
            $methodNegotiator,
            $headersNegotiator,
            $exposeHeaders,
            $allowCredentials,
            $maxAge
        );
    }
}
