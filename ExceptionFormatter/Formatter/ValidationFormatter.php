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

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\FormattedError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;

#[AsService, AsTagged(FormatterInterface::class)]
class ValidationFormatter implements FormatterInterface
{
    public function __invoke(ValidationException $e): ErrorContract
    {
        return new FormattedError(
            $e->getMessage(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::formatErrors(...$e->errors),
            $e->getTrace(),
        );
    }

    public static function getExceptionClass(): string
    {
        return ValidationException::class;
    }

    public static function getPriority(): int
    {
        return 1;
    }

    private static function formatErrors(Error ...$errors): array
    {
        return ArrayList::collect($errors)
            ->map(static fn (Error $error) => [$error->getField() => $error->getMessage()])
            ->toMergedArray()
        ;
    }
}
