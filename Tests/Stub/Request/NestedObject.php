<?php

declare(strict_types=1);


namespace FRZB\Component\RequestMapper\Tests\Stub\Request;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/** @internal */
#[Immutable]
final class NestedObject
{
    #[NotBlank]
    #[Type('string')]
    public readonly string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }
}
