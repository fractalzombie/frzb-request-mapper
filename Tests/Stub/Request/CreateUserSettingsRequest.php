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

use JetBrains\PhpStorm\Pure;

/**
 * @internal
 */
class CreateUserSettingsRequest extends CreateSettingsRequest
{
    public const TYPE = 'user';

    #[Pure]
    public function __construct(
        string $type,
        private string $name,
    ) {
        parent::__construct($type);
    }

    public function getName(): string
    {
        return $this->name;
    }
}
