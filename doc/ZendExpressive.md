# Zend Expressive

## Requirements

* [zendframework/zend-expressive][1]: ^3.2.1

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
use Zend\Diactoros\ResponseFactory;
use Zend\Expressive\Application;

$app = new Application();

$app->pipe(new CorsMiddleware(
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

[1]: https://packagist.org/packages/zendframework/zend-expressive
