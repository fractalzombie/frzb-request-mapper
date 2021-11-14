<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[DiscriminatorMap('type', [
    CreateUserSettingsRequest::TYPE => CreateUserSettingsRequest::class,
    CreateCardSettingsRequest::TYPE => CreateCardSettingsRequest::class,
])]
abstract class CreateSettingsRequest
{
    public function __construct(
        protected string $type
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }
}
