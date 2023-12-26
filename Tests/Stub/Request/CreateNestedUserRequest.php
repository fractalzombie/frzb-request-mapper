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

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @internal
 */
class CreateNestedUserRequest
{
    #[Required]
    #[NotBlank]
    #[Type('string')]
    private string $name;

    #[Valid]
    #[Type(CreateUserRequest::class)]
    private ?CreateUserRequest $request;

    public function __construct(string $name, ?CreateUserRequest $request = null)
    {
        $this->name = $name;
        $this->request = $request;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequest(): ?CreateUserRequest
    {
        return $this->request;
    }
}
