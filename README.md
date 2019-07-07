# chubbyphp-cors

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-cors.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-cors)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-cors/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-cors/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-cors/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-cors/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)

## Description

A minimal Cors implementation for PSR 15.

## Requirements

 * php: ^7.2
 * [psr/http-factory][2]: ^1.0.1
 * [psr/http-message][3]: ^1.0.1
 * [psr/http-server-middleware][4]: ^1.0.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-cors][1].

## Usage

### chubbyphp-framework

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\Framework\Application;
use Chubbyphp\Framework\ErrorHandler;
use Chubbyphp\Framework\ExceptionHandler;
use Chubbyphp\Framework\Middleware\MiddlewareDispatcher;
use Chubbyphp\Framework\Router\FastRouteRouter;
use Chubbyphp\Framework\Router\Route;
use Zend\Diactoros\ResponseFactory;

$responseFactory = new ResponseFactory();

$route = Route::options('/{path:.*}', 'cors_preflight', new CorsPreflightRequestHandler(
    $responseFactory,
    new MethodNegotiator(['GET', 'POST']), // allow-method
    new HeadersNegotiator(['X-Custom-Request']), // allow-headers
    7200
))->middleware(new CorsMiddleware(
    new OriginNegotiator([
        new AllowOriginExact('https://myproject.com'),
        new AllowOriginRegex('^https://myproject\.'),
    ]), // allow-origin
    true, // allow-credentials
    ['X-Custom-Response'] // expose-headers
));

$app = new Application(
    new FastRouteRouter([$route]),
    new MiddlewareDispatcher(),
    new ExceptionHandler($responseFactory, true)
);
```

### slim

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\Cors\Negotiation\HeadersNegotiator;
use Chubbyphp\Cors\Negotiation\MethodNegotiator;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginExact;
use Chubbyphp\Cors\Negotiation\Origin\AllowOriginRegex;
use Chubbyphp\Cors\Negotiation\Origin\OriginNegotiator;
use Chubbyphp\SlimPsr15\MiddlewareAdapter;
use Chubbyphp\SlimPsr15\RequestHandlerAdapter;
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

$app->options('/{path:.*}', new RequestHandlerAdapter(new CorsPreflightRequestHandler(
    $responseFactory,
    new MethodNegotiator(['GET', 'POST']), // allow-method
    new HeadersNegotiator(['X-Custom-Request']), // allow-headers
    7200
)))->setName('cors_preflight')->add(new MiddlewareAdapter(new CorsMiddleware(
    new OriginNegotiator([
        new AllowOriginExact('https://myproject.com'),
        new AllowOriginRegex('^https://myproject\.'),
    ]), // allow-origin
    true, // allow-credentials
    ['X-Custom-Response'] // expose-headers
)));
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-cors

[2]: https://packagist.org/packages/psr/http-factory
[3]: https://packagist.org/packages/psr/http-message
[4]: https://packagist.org/packages/psr/http-server-middleware
