<?php

declare(strict_types=1);

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
