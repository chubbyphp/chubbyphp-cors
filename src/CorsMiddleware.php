<?php

declare(strict_types=1);

namespace Chubbyphp\Cors;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class CorsMiddleware implements MiddlewareInterface
{
    /**
     * @var string[]
     */
    private $allowOrigin;

    /**
     * @var bool
     */
    private $allowCredentials;

    /**
     * @param string[] $allowOrigin
     * @param bool     $allowCredentials
     */
    public function __construct(array $allowOrigin, bool $allowCredentials = false)
    {
        $this->allowOrigin = $allowOrigin;
        $this->allowCredentials = $allowCredentials;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $handler->handle($request)
            ->withHeader('Access-Control-Allow-Origin', $this->getAllowOrigin($request))
            ->withHeader('Access-Control-Allow-Credentials', $this->allowCredentials ? 'true' : 'false')
        ;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getAllowOrigin(ServerRequestInterface $request): string
    {
        if ('' === $origin = $request->getHeaderLine('Origin')) {
            return '';
        }

        foreach ($this->allowOrigin as $allowOrigin) {
            if ('~' === $allowOrigin[0] ?? '') {
                if (1 === preg_match('!'.substr($allowOrigin, 1).'!', $origin)) {
                    return $origin;
                }
            } else {
                if ($allowOrigin === $origin) {
                    return $origin;
                }
            }
        }

        return '';
    }
}
