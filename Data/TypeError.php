<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Exception\TypeErrorInvalidArgumentException;
use FRZB\Component\RequestMapper\Helper\ObjectHelper;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class TypeError
{
    public function __construct(
        private readonly string $class,
        private readonly string $method,
        private readonly int $position,
        private readonly string $expected,
        private readonly string $proposed,
    ) {
    }

    public static function fromArray(array $params): self
    {
        if (!ObjectHelper::isArrayHasAllPropertiesFromClass($params, self::class)) {
            throw TypeErrorInvalidArgumentException::fromParams($params);
        }

        return new self($params['class'], $params['method'], (int) $params['position'], $params['expected'], $params['proposed']);
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getExpected(): string
    {
        return $this->expected;
    }

    public function getProposed(): string
    {
        return $this->proposed;
    }
}
