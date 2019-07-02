<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

interface AllowOriginInterface
{
    /**
     * @param string $origin
     *
     * @return bool
     */
    public function match(string $origin): bool;
}
