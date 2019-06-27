<?php

declare(strict_types=1);

namespace Chubbyphp\Cors;

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
     * @var string[]
     */
    private $allowMethods;

    /**
     * @var string[]
     */
    private $allowHeaders;

    /**
     * @var int
     */
    private $maxAge;

    /**
     * @param ResponseFactoryInterface $responseFactory
     * @param string[]                 $allowMethods
     * @param string[]                 $allowHeaders
     * @param int                      $maxAge
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        array $allowMethods,
        array $allowHeaders,
        int $maxAge = 600
    ) {
        $this->responseFactory = $responseFactory;
        $this->allowMethods = $allowMethods;
        $this->allowHeaders = $allowHeaders;
        $this->maxAge = $maxAge;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(204)
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->allowMethods))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->allowHeaders))
            ->withHeader('Access-Control-Max-Age', (string) $this->maxAge)
        ;
    }
}
