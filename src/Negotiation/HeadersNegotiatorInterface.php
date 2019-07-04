<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

interface HeadersNegotiatorInterface
{
    const HEADER = 'Access-Control-Request-Headers';

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function negotiate(ServerRequestInterface $request): bool;

    /**
     * @return string[]
     */
    public function getAllowedHeaders(): array;
}
