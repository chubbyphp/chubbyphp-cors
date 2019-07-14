<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

final class MethodNegotiator implements MethodNegotiatorInterface
{
    /**
     * @var string[]
     */
    private $allowMethods;

    /**
     * @param string[] $allowMethods
     */
    public function __construct(array $allowMethods)
    {
        $this->allowMethods = [];
        foreach ($allowMethods as $allowMethod) {
            $this->addAllowMethod($allowMethod);
        }
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function negotiate(ServerRequestInterface $request): bool
    {
        if ('' === $method = $request->getHeaderLine(self::HEADER)) {
            return false;
        }

        foreach ($this->allowMethods as $allowMethod) {
            if ($allowMethod === $method) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function getAllowedMethods(): array
    {
        return $this->allowMethods;
    }

    /**
     * @param string $allowMethod
     */
    private function addAllowMethod(string $allowMethod): void
    {
        $this->allowMethods[] = $allowMethod;
    }
}
