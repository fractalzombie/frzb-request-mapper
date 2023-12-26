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

namespace FRZB\Component\RequestMapper\Tests\Stub\Request;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

/**
 * @internal
 */
#[DiscriminatorMap('type', [
    CreateUserSettingsRequest::TYPE => CreateUserSettingsRequest::class,
    CreateCardSettingsRequest::TYPE => CreateCardSettingsRequest::class,
])]
abstract class CreateSettingsRequest
{
    public function __construct(
        protected string $type
    ) {}

    public function getType(): string
    {
        return $this->type;
    }
}
