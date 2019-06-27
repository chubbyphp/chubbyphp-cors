# chubbyphp-cors

[![Build Status](https://api.travis-ci.org/chubbyphp/chubbyphp-cors.png?branch=master)](https://travis-ci.org/chubbyphp/chubbyphp-cors)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/chubbyphp/chubbyphp-cors/?branch=master)
[![Total Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-cors/downloads.png)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Monthly Downloads](https://poser.pugx.org/chubbyphp/chubbyphp-cors/d/monthly)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Latest Stable Version](https://poser.pugx.org/chubbyphp/chubbyphp-cors/v/stable.png)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)
[![Latest Unstable Version](https://poser.pugx.org/chubbyphp/chubbyphp-cors/v/unstable)](https://packagist.org/packages/chubbyphp/chubbyphp-cors)

## Description

A minimal CORS implementation as a middleware.

## Requirements

 * php: ^7.2
 * [psr/http-factory][2]: ^1.0.1
 * [psr/http-message][3]: ^1.0.1
 * [psr/http-server-middleware][4]: ^1.0.1

## Installation

Through [Composer](http://getcomposer.org) as [chubbyphp/chubbyphp-cors][1].

## Usage

### Slim

```php
<?php

declare(strict_types=1);

namespace App;

use Chubbyphp\Cors\CorsMiddleware;
use Chubbyphp\Cors\CorsPreflightRequestHandler;
use Chubbyphp\SlimPsr15\MiddlewareAdapter;
use Chubbyphp\SlimPsr15\RequestHandlerAdapter;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\App;

/** @var ResponseFactoryInterface $responseFactory */
$responseFactory = ...;

$app = new App();

$app->add(new MiddlewareAdapter(new CorsMiddleware(
    ['https://myproject.com', '~^https://myproject\.'], // allow-origin
    true // allow-credentials
)));

$app->options('/{path:.*}', new RequestHandlerAdapter(new CorsPreflightRequestHandler(
    $responseFactory,
    ['GET', 'POST'], // allow-methods
    ['Accept', 'Content-Type'], // allow-headers
    120 // max-age
)));
```

## Copyright

Dominik Zogg 2019

[1]: https://packagist.org/packages/chubbyphp/chubbyphp-cors

[2]: https://packagist.org/packages/psr/http-factory
[3]: https://packagist.org/packages/psr/http-message
[4]: https://packagist.org/packages/psr/http-server-middleware
