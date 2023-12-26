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

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\HashMap;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\HttpFoundation\Request;

/** @internal */
#[Immutable]
final class HeaderHelper
{
    private function __construct() {}

    public static function getHeaders(Request $request): array
    {
        return HashMap::collect($request->headers->all())
            ->mapKV(static fn (string $key, array $value) => [$key => current($value) ?: null])
            ->toMergedArray()
        ;
    }
}
