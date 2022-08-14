<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class ExceptionMapperException extends \LogicException
{
    private const NOT_MATCHED_GROUP_MESSAGE = 'Regex group "%s" not matched in "%s" exception with message "%s"';

    public static function notMatchedGroup(string $groupName, \Throwable $previous): self
    {
        $message = sprintf(self::NOT_MATCHED_GROUP_MESSAGE, $groupName, $previous::class, $previous->getMessage());

        return new self($message, previous: $previous);
    }
}
