# Slim 3

## Requirements

 * [http-interop/http-factory-slim][1]: ^2.0
 * [slim/slim][2]: ^3.12.1

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
use Http\Factory\Slim\ResponseFactory;
use Slim\App;

$app = new App();

$app->add(new MiddlewareAdapter(
    new CorsMiddleware(
        new ResponseFactory(),
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

[1]: https://packagist.org/packages/http-interop/http-factory-slim
[2]: https://packagist.org/packages/slim/slim
