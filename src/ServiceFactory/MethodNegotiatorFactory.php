<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\ServiceFactory;

use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Laminas\Config\Factory\AbstractFactory;
use Psr\Container\ContainerInterface;

final class MethodNegotiatorFactory extends AbstractFactory
{
    public function __invoke(ContainerInterface $container): MethodNegotiator
    {
        $config = $this->resolveConfig($container->get('config')['chubbyphp']['cors'] ?? []);

        return new MethodNegotiator($config['allowMethods'] ?? []);
    }
}
