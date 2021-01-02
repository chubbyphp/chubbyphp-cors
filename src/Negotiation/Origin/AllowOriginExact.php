<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

final class AllowOriginExact implements AllowOriginInterface
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function match(string $origin): bool
    {
        return $this->value === $origin;
    }
}
