<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

use Psr\Http\Message\ServerRequestInterface;

final class OriginNegotiator implements OriginNegotiatorInterface
{
    /**
     * @var array<AllowOriginInterface>
     */
    private $allowOrigins;

    /**
     * @param array<AllowOriginInterface> $allowOrigins
     */
    public function __construct(array $allowOrigins)
    {
        $this->allowOrigins = [];
        foreach ($allowOrigins as $allowOrigin) {
            $this->addAllowOrigin($allowOrigin);
        }
    }

    public function negotiate(ServerRequestInterface $request): ?string
    {
        if ('' === $origin = $request->getHeaderLine(self::HEADER)) {
            return null;
        }

        foreach ($this->allowOrigins as $allowOrigin) {
            if ($allowOrigin->match($origin)) {
                return $origin;
            }
        }

        return null;
    }

    private function addAllowOrigin(AllowOriginInterface $allowOrigin): void
    {
        $this->allowOrigins[] = $allowOrigin;
    }
}
