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
        /** @var array{chubbyphp?: array{cors?: array<string, mixed>}} $config */
        $config = $container->get('config');

        /** @var array{allowMethods?: array<string>} $corsConfig */
        $corsConfig = $this->resolveConfig($config['chubbyphp']['cors'] ?? []);

        $allowMethods = $corsConfig['allowMethods'] ?? [];

        return new MethodNegotiator($allowMethods);
    }
}
