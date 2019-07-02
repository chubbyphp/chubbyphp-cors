<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation\Origin;

use Psr\Http\Message\ServerRequestInterface;

final class OriginNegotiator implements OriginNegotiatorInterface
{
    /**
     * @var AllowOriginInterface[]
     */
    private $allowOrigins;

    /**
     * @param AllowOriginInterface[] $allowOrigins
     */
    public function __construct(array $allowOrigins)
    {
        $this->allowOrigins = [];
        foreach ($allowOrigins as $allowOrigin) {
            $this->addAllowOrigin($allowOrigin);
        }
    }

    /**
     * @param AllowOriginInterface $allowOrigin
     */
    private function addAllowOrigin(AllowOriginInterface $allowOrigin): void
    {
        $this->allowOrigins[] = $allowOrigin;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
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
}
