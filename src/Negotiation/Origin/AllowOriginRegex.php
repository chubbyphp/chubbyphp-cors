<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

final class AllowOriginRegex implements AllowOriginInterface
{
    public function __construct(private string $pattern) {}

    public function match(string $origin): bool
    {
        return 1 === preg_match('!'.$this->pattern.'!', $origin);
    }
}
