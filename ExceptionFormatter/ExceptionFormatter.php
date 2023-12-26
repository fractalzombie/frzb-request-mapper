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

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Data\ErrorContract as ContractError;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;

#[AsService]
class ExceptionFormatter implements ExceptionFormatterInterface
{
    public function __construct(
        private readonly ExceptionFormatterLocator $formatterLocator,
    ) {}

    public function format(\Throwable $e): ContractError
    {
        return $this->formatterLocator->get($e)($e);
    }
}
