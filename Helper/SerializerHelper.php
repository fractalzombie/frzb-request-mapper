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

use Fp\Collections\ArrayList;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\SerializedName;

/** @internal */
#[Immutable]
final class SerializerHelper
{
    private function __construct() {}

    public static function getSerializedNameAttribute(\ReflectionParameter|\ReflectionProperty $rProperty): SerializedName
    {
        return ArrayList::collect(AttributeHelper::getAttributes($rProperty, SerializedName::class))
            ->firstElement()
            ->getOrElse(new SerializedName($rProperty->getName()))
        ;
    }
}
