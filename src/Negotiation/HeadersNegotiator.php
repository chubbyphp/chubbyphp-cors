<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

final class HeadersNegotiator implements HeadersNegotiatorInterface
{
    /**
     * @var array<string>
     */
    private $allowHeaders;

    /**
     * @param array<string> $allowHeaders
     */
    public function __construct(array $allowHeaders)
    {
        $this->allowHeaders = [];
        foreach ($allowHeaders as $allowHeader) {
            $this->addAllowHeader($allowHeader);
        }
    }

    public function negotiate(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader(self::HEADER)) {
            return false;
        }

        $headers = $this->getHeaders($request);
        foreach ($headers as $i => $header) {
            foreach ($this->allowHeaders as $allowHeader) {
                if (strtolower($allowHeader) !== strtolower($header)) {
                    continue;
                }

                unset($headers[$i]);
            }
        }

        return [] === $headers;
    }

    /**
     * @return array<string>
     */
    public function getAllowedHeaders(): array
    {
        return $this->allowHeaders;
    }

    private function addAllowHeader(string $allowHeader): void
    {
        $this->allowHeaders[] = $allowHeader;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array<string>
     */
    private function getHeaders(ServerRequestInterface $request): array
    {
        $headers = [];
        foreach (explode(',', $request->getHeaderLine(self::HEADER)) as $header) {
            $headers[] = trim($header);
        }

        return $headers;
    }
}
