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

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Exception\TypeErrorInvalidArgumentException;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class TypeError
{
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly int $position,
        public readonly string $expected,
        public readonly string $proposed,
    ) {}

    public static function fromArray(array $params): self
    {
        if (!ClassHelper::isArrayHasAllPropertiesFromClass($params, self::class)) {
            throw TypeErrorInvalidArgumentException::fromParams($params);
        }

        return new self($params['class'], $params['method'], (int) $params['position'], $params['expected'], $params['proposed']);
    }
}
