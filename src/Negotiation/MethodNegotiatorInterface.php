<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

interface MethodNegotiatorInterface
{
    public const HEADER = 'Access-Control-Request-Method';

    public function negotiate(ServerRequestInterface $request): bool;

    /**
     * @return array<string>
     */
    public function getAllowedMethods(): array;
}
