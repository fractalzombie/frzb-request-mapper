<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub\Request;

use FRZB\Component\RequestMapper\Attribute\ArrayType;
use FRZB\Component\RequestMapper\Tests\Stub\Enum\TestEnum;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Contracts\Service\Attribute\Required;

/** @internal */
class CreateUserRequestWithEnumAndNestedObjectAndArrayOfObjects
{
    #[Required]
    #[NotBlank]
    #[Type('string')]
    public string $name;

    #[Uuid]
    #[Type('string')]
    public ?string $userId;

    #[Type('float')]
    public ?float $amount;

    #[Type('string')]
    public ?TestEnum $testEnum;

    #[Valid]
    #[Type(NestedObject::class)]
    public ?NestedObject $nested;

    #[Valid]
    #[Type('array')]
    #[ArrayType(NestedObject::class)]
    public array $nestedObjects;

    public function __construct(
        string $name,
        ?NestedObject $nested = null,
        ?string $userId = null,
        ?float $amount = null,
        ?TestEnum $testEnum = null,
        array $nestedObjects = [],
    ) {
        $this->name = $name;
        $this->nested = $nested;
        $this->userId = $userId;
        $this->amount = $amount;
        $this->testEnum = $testEnum;
        $this->nestedObjects = $nestedObjects;
    }
}
