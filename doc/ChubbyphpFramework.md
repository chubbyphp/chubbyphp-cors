# Chubbyphp Framework

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
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Zend\Diactoros\ResponseFactory;

$responseFactory = new ResponseFactory();

$app = new Application(
    new FastRouteRouter([]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true),
    [
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
    ]
);
```
