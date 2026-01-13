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
        /** @var array{chubbyphp?: array{cors?: array<string, mixed>}} $config */
        $config = $container->get('config');

        /** @var array{allowHeaders?: array<string>} $corsConfig */
        $corsConfig = $this->resolveConfig($config['chubbyphp']['cors'] ?? []);

        $allowHeaders = $corsConfig['allowHeaders'] ?? [];

        return new HeadersNegotiator($allowHeaders);
    }
}
