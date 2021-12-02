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
        $config = $this->resolveConfig($container->get('config')['chubbyphp']['cors'] ?? []);

        $allowOrigins = [];
        foreach ($config['allowOrigins'] ?? [] as $allowOrigin => $class) {
            /** @var AllowOriginInterface $allowOrigin */
            $allowOrigin = new $class($allowOrigin);
            $allowOrigins[] = $allowOrigin;
        }

        return new OriginNegotiator($allowOrigins);
    }
}
