<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Helper;

/** @internal */
interface TestConstant
{
    public const UUID = '46cfc29f-36d7-479c-a8f5-470ebef78c47';
    public const EXCEPTION_MESSAGE = 'Something goes wrong';

    public const USER_NAME = 'user';
    public const USER_ID = self::UUID;
    public const USER_AMOUNT = 123.10;
}
