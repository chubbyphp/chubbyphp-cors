<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

interface AllowOriginInterface
{
    public function match(string $origin): bool;
}
