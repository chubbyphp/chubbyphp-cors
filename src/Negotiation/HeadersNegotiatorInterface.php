<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

interface HeadersNegotiatorInterface
{
    public const HEADER = 'Access-Control-Request-Headers';

    public function negotiate(ServerRequestInterface $request): bool;

    /**
     * @return array<string>
     */
    public function getAllowedHeaders(): array;
}
