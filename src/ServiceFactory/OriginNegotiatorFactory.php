<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\ServiceFactory;

use Chubbyphp\Cors\Negotiation\Origin\AllowOriginInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class OriginNegotiatorFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): OriginNegotiator
    {
        /** @var array{chubbyphp?: array{cors?: array<string, mixed>}} $config */
        $config = $container->get('config');

        /** @var array{allowOrigins?: array<string, class-string<AllowOriginInterface>>} $corsConfig */
        $corsConfig = $this->resolveConfig($config['chubbyphp']['cors'] ?? []);

        $allowOrigins = [];

        $allowOriginsConfig = $corsConfig['allowOrigins'] ?? [];

        foreach ($allowOriginsConfig as $allowOrigin => $class) {
            /** @var AllowOriginInterface $allowOriginInstance */
            $allowOriginInstance = new $class($allowOrigin);
            $allowOrigins[] = $allowOriginInstance;
        }

        return new OriginNegotiator($allowOrigins);
    }
}
