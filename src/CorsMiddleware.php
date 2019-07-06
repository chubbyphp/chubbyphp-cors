<?php

declare(strict_types=1);

namespace Chubbyphp\Cors;

use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @var OriginNegotiatorInterface
     */
    private $originNegotiator;

    /**
     * @var bool
     */
    private $allowCredentials;

    /**
     * @var array
     */
    private $exposeHeaders;

    /**
     * @param OriginNegotiatorInterface $originNegotiator
     * @param bool                      $allowCredentials
     * @param array                     $exposeHeaders
     */
    public function __construct(
        OriginNegotiatorInterface $originNegotiator,
        bool $allowCredentials = false,
        array $exposeHeaders = []
    ) {
        $this->originNegotiator = $originNegotiator;
        $this->allowCredentials = $allowCredentials;
        $this->exposeHeaders = $exposeHeaders;
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

        $request = $request->withAttribute('allowOrigin', $allowOrigin);

        $response = $handler->handle($request);

        if (null !== $allowOrigin) {
            $response = $response
                ->withHeader('Access-Control-Allow-Origin', $allowOrigin)
                ->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials ? 'true' : 'false')
            ;

            if ([] !== $this->exposeHeaders) {
                $response = $response->withHeader('Access-Control-Expose-Headers', implode(', ', $this->exposeHeaders));
            }
        }

        return $response;
    }
}
