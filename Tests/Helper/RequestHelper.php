<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Helper;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

/** @internal */
#[Immutable]
final class RequestHelper
{
    private function __constructor(): void
    {
    }

    public static function makeRequest(
        string $method,
        array $params = [],
        array $headers = [],
        ?string $content = null,
    ): Request {
        $request = Request::createFromGlobals();
        $attributes = [
            '_route' => '\some\path\to\url',
            '_controller' => 'SomeNameSpace\IndexController',
            '_stopwatch_token' => (string) Uuid::v4() ?? 'c14b12b1-eff6-4ff0-afe8-e9d0b0d97a2a',
            '_route_params' => $params,
        ];

        $request->initialize($params, $params, $attributes, $_COOKIE, $_FILES, $_SERVER, $content);
        $request->setMethod($method);
        $request->headers = new HeaderBag($headers);

        return $request;
    }
}
