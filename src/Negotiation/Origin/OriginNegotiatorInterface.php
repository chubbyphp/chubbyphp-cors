<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

use Psr\Http\Message\ServerRequestInterface;

interface OriginNegotiatorInterface
{
    public const HEADER = 'Origin';

    public function negotiate(ServerRequestInterface $request): ?string;
}
