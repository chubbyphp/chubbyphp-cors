<?php

declare(strict_types=1);

namespace Chubbyphp\Cors;

use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsPreflightRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var MethodNegotiatorInterface
     */
    private $methodNegotiator;

    /**
     * @var HeadersNegotiatorInterface
     */
    private $headersNegotiator;

    /**
     * @var int
     */
    private $maxAge;

    /**
     * @param ResponseFactoryInterface   $responseFactory
     * @param MethodNegotiatorInterface  $methodNegotiator
     * @param HeadersNegotiatorInterface $headersNegotiator
     * @param int                        $maxAge
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        MethodNegotiatorInterface $methodNegotiator,
        HeadersNegotiatorInterface $headersNegotiator,
        int $maxAge = 600
    ) {
        $this->responseFactory = $responseFactory;
        $this->methodNegotiator = $methodNegotiator;
        $this->headersNegotiator = $headersNegotiator;
        $this->maxAge = $maxAge;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(204);

        if (null === $request->getAttribute('allowOrigin')) {
            return $response;
        }

        if (!$this->methodNegotiator->negotiate($request)) {
            return $response;
        }

        $response = $response->withHeader(
            'Access-Control-Allow-Methods',
            implode(', ', $this->methodNegotiator->getAllowedMethods())
        );

        if (!$this->headersNegotiator->negotiate($request)) {
            return $response;
        }

        $response = $response->withHeader(
            'Access-Control-Allow-Headers',
            implode(', ', $this->headersNegotiator->getAllowedHeaders())
        );

        return $response->withHeader('Access-Control-Max-Age', (string) $this->maxAge);
    }
}
