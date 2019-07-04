<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

use Psr\Http\Message\ServerRequestInterface;

interface OriginNegotiatorInterface
{
    const HEADER = 'Origin';

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function negotiate(ServerRequestInterface $request): ?string;
}
