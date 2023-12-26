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

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @internal
 */
class CreateUserWithSerializedNameRequest
{
    #[Required]
    #[NotBlank]
    #[Type('string')]
    private string $name;

    #[SerializedName('uuid')]
    #[Uuid]
    #[Type('string')]
    private ?string $userId;

    #[SerializedName('amountOfWallet')]
    #[Type('float')]
    private ?float $amount;

    public function __construct(string $name, ?string $userId = null, ?float $amount = null)
    {
        $this->name = $name;
        $this->userId = $userId;
        $this->amount = $amount;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }
}
