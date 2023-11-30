<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

final class AllowOriginExact implements AllowOriginInterface
{
    public function __construct(private string $value) {}

    public function match(string $origin): bool
    {
        return $this->value === $origin;
    }
}
