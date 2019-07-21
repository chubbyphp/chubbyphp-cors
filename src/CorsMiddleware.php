<?php

declare(strict_types=1);

namespace Chubbyphp\Cors;

use Chubbyphp\Cors\Negotiation\HeadersNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\MethodNegotiatorInterface;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $responseFactory;

    /**
     * @var OriginNegotiatorInterface
     */
    private $originNegotiator;

    /**
     * @var MethodNegotiatorInterface
     */
    private $methodNegotiator;

    /**
     * @var HeadersNegotiatorInterface
     */
    private $headersNegotiator;

    /**
     * @var array
     */
    private $exposeHeaders;

    /**
     * @var bool
     */
    private $allowCredentials;

    /**
     * @var int
     */
    private $maxAge;

    /**
     * @param ResponseFactoryInterface   $responseFactory
     * @param OriginNegotiatorInterface  $originNegotiator
     * @param MethodNegotiatorInterface  $methodNegotiator
     * @param HeadersNegotiatorInterface $headersNegotiator
     * @param string[]                   $exposeHeaders
     * @param bool                       $allowCredentials
     * @param int                        $maxAge
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        OriginNegotiatorInterface $originNegotiator,
        MethodNegotiatorInterface $methodNegotiator,
        HeadersNegotiatorInterface $headersNegotiator,
        array $exposeHeaders = [],
        bool $allowCredentials = false,
        int $maxAge = 600
    ) {
        $this->responseFactory = $responseFactory;
        $this->originNegotiator = $originNegotiator;
        $this->methodNegotiator = $methodNegotiator;
        $this->headersNegotiator = $headersNegotiator;
        $this->exposeHeaders = $exposeHeaders;
        $this->allowCredentials = $allowCredentials;
        $this->maxAge = $maxAge;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $allowOrigin = $this->originNegotiator->negotiate($request);

        if ($this->isPreflight($request)) {
            return $this->handlePreflight($request, $allowOrigin);
        }

        return $this->handle($request, $handler, $allowOrigin);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function isPreflight(ServerRequestInterface $request): bool
    {
        return 'OPTIONS' === strtoupper($request->getMethod());
    }

    /**
     * @param ServerRequestInterface $request
     * @param string|null            $allowOrigin
     *
     * @return ResponseInterface
     */
    private function handlePreflight(ServerRequestInterface $request, ?string $allowOrigin): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(204);

        if (null === $allowOrigin) {
            return $response;
        }

        $response = $this->addAllowOrigin($response, $allowOrigin);
        $response = $this->addAllowCredentials($response);
        $response = $this->addExposeHeaders($response);

        if (!$this->methodNegotiator->negotiate($request)) {
            return $response;
        }

        $response = $this->addAllowMethod($response);

        if (!$this->headersNegotiator->negotiate($request)) {
            return $response;
        }

        $response = $this->addAllowHeaders($response);

        return $this->addMaxAge($response);
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     * @param string|null             $allowOrigin
     *
     * @return ResponseInterface
     */
    private function handle(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ?string $allowOrigin
    ): ResponseInterface {
        $response = $handler->handle($request);

        if (null === $allowOrigin) {
            return $response;
        }

        $response = $this->addAllowOrigin($response, $allowOrigin);
        $response = $this->addAllowCredentials($response);

        return $this->addExposeHeaders($response);
    }

    /**
     * @param ResponseInterface $response
     * @param string            $allowOrigin
     *
     * @return ResponseInterface
     */
    private function addAllowOrigin(ResponseInterface $response, string $allowOrigin): ResponseInterface
    {
        return $response->withHeader('Access-Control-Allow-Origin', $allowOrigin);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function addAllowCredentials(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials ? 'true' : 'false');
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function addExposeHeaders(ResponseInterface $response): ResponseInterface
    {
        if ([] === $this->exposeHeaders) {
            return $response;
        }

        return $response->withHeader('Access-Control-Expose-Headers', implode(', ', $this->exposeHeaders));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function addAllowMethod(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader(
            'Access-Control-Allow-Methods',
            implode(', ', $this->methodNegotiator->getAllowedMethods())
        );
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function addAllowHeaders(ResponseInterface $response): ResponseInterface
    {
        return $response->withHeader(
            'Access-Control-Allow-Headers',
            implode(', ', $this->headersNegotiator->getAllowedHeaders())
        );
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function addMaxAge(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('Access-Control-Max-Age', (string) $this->maxAge)
            ->withHeader('Cache-Control', 'public, max-age='.$this->maxAge)
        ;
    }
}
