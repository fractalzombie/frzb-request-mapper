<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use JetBrains\PhpStorm\Pure;

/**
 * @internal
 */
class CreateCardSettingsRequest extends CreateSettingsRequest
{
    public const TYPE = 'card';

    #[Pure]
    public function __construct(
        string $type,
        private string $primaryAccountNumber,
    ) {
        parent::__construct($type);
    }

    public function getPrimaryAccountNumber(): string
    {
        return $this->primaryAccountNumber;
    }
}
