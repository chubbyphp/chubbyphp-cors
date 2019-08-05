# Slim 3

## Example

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\SlimPsr15\MiddlewareAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Http\Response;

$responseFactory = new class() implements ResponseFactoryInterface
{
    /**
     * @param int    $code
     * @param string $reasonPhrase
     *
     * @return ResponseInterface
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        $response = new Response($code);
        if ('' !== $reasonPhrase) {
            $response = $response->withStatus($code, $reasonPhrase);
        }

        return $response;
    }
};

$app = new App();

$app->add(new MiddlewareAdapter(
    new CorsMiddleware(
        $responseFactory,
        new OriginNegotiator([
            new AllowOriginExact('https://myproject.com'),
            new AllowOriginRegex('^https://myproject\.'),
        ]), // allow-origin
        new MethodNegotiator(['GET', 'POST']), // allow-method
        new HeadersNegotiator(['X-Custom-Request']), // allow-headers
        ['X-Custom-Response'], // expose-headers
        true, // allow-credentials
        7200 // max age
    )
));
```
