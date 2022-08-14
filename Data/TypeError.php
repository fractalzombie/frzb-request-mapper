<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Exception\TypeErrorInvalidArgumentException;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class TypeError
{
    public function __construct(
        public readonly string $class,
        public readonly string $method,
        public readonly int $position,
        public readonly string $expected,
        public readonly string $proposed,
    ) {
    }

    public static function fromArray(array $params): self
    {
        if (!ClassHelper::isArrayHasAllPropertiesFromClass($params, self::class)) {
            throw TypeErrorInvalidArgumentException::fromParams($params);
        }

        return new self($params['class'], $params['method'], (int) $params['position'], $params['expected'], $params['proposed']);
    }
}
