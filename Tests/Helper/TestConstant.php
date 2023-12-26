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

/**
 * @internal
 */
interface TestConstant
{
    public const UUID = '46cfc29f-36d7-479c-a8f5-470ebef78c47';
    public const EXCEPTION_MESSAGE = 'Something goes wrong';
    public const TYPE_ERROR_EXCEPTION_MESSAGE = 'FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest::__construct(): Argument #1 ($name) must be of type string, array given, called in /some/path/SomeFile.php on line 16 and defined in /some/path/SomeFile.php on line 20';

    public const USER_NAME = 'user';
    public const USER_ID = self::UUID;
    public const USER_AMOUNT = 123.10;
}
