<?php

declare(strict_types=1);

namespace Chubbyphp\Cors\Negotiation;

use Psr\Http\Message\ServerRequestInterface;

final class HeadersNegotiator implements HeadersNegotiatorInterface
{
    /**
     * @var string[]
     */
    private $allowHeaders;

    /**
     * @param string[] $allowHeaders
     */
    public function __construct(array $allowHeaders)
    {
        $this->allowHeaders = [];
        foreach ($allowHeaders as $allowHeader) {
            $this->addAllowHeader($allowHeader);
        }
    }

    /**
     * @param string $allowHeader
     */
    private function addAllowHeader(string $allowHeader): void
    {
        $this->allowHeaders[] = $allowHeader;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    public function negotiate(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader(self::HEADER)) {
            return false;
        }

        $headers = $this->getHeaders($request);
        foreach ($headers as $i => $header) {
            foreach ($this->allowHeaders as $allowHeader) {
                if (mb_strtolower($allowHeader) !== mb_strtolower($header)) {
                    continue;
                }

                unset($headers[$i]);
            }
        }

        return [] === $headers;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string[]
     */
    private function getHeaders(ServerRequestInterface $request): array
    {
        $headers = [];
        foreach (explode(',', $request->getHeaderLine(self::HEADER)) as $header) {
            $headers[] = trim($header);
        }

        return $headers;
    }

    /**
     * @return string[]
     */
    public function getAllowedHeaders(): array
    {
        return $this->allowHeaders;
    }
}
