<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

interface MethodNegotiatorInterface
{
    const HEADER = 'Access-Control-Request-Method';

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function negotiate(ServerRequestInterface $request): bool;

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array;
}
