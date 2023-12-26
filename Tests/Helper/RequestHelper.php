<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Tests\Helper;

use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

/**
 * @internal
 */
final class RequestHelper
{
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
            '_stopwatch_token' => (string) Uuid::v4(),
            '_route_params' => $params,
        ];

        $request->initialize($params, $params, $attributes, $_COOKIE, $_FILES, $_SERVER, $content);
        $request->setMethod($method);
        $request->headers = new HeaderBag($headers);

        return $request;
    }
}
