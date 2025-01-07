<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

final class MethodNegotiator implements MethodNegotiatorInterface
{
    /**
     * @var array<string>
     */
    private array $allowMethods = [];

    /**
     * @param array<string> $allowMethods
     */
    public function __construct(array $allowMethods)
    {
        foreach ($allowMethods as $allowMethod) {
            $this->addAllowMethod($allowMethod);
        }
    }

    public function negotiate(ServerRequestInterface $request): bool
    {
        if ('' === $method = $request->getHeaderLine(self::HEADER)) {
            return false;
        }

        return \in_array($method, $this->allowMethods, true);
    }

    /**
     * @return array<string>
     */
    public function getAllowedMethods(): array
    {
        return $this->allowMethods;
    }

    private function addAllowMethod(string $allowMethod): void
    {
        $this->allowMethods[] = $allowMethod;
    }
}
