<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\ServiceFactory;

use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class HeadersNegotiatorFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): HeadersNegotiator
    {
        $config = $this->resolveConfig($container->get('config')['chubbyphp']['cors'] ?? []);

        return new HeadersNegotiator($config['allowHeaders'] ?? []);
    }
}
