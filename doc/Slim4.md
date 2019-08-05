# Slim 4

## Requirements

 * [slim/slim][2]: ^4.0

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
use Slim\App;
use Slim\Psr7\Factory\ResponseFactory;

$app = new App();

$app->add(new CorsMiddleware(
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
));
```

[2]: https://packagist.org/packages/slim/slim
