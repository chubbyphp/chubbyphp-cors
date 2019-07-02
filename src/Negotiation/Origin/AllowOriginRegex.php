<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

final class AllowOriginRegex implements AllowOriginInterface
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * @param string $origin
     *
     * @return bool
     */
    public function match(string $origin): bool
    {
        return 1 === preg_match('!'.$this->pattern.'!', $origin);
    }
}
