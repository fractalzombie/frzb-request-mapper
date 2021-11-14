<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use FRZB\Component\RequestMapper\Utils\ObjectUtil;

final class TypeError
{
    public const NOT_ALL_PARAMETERS_TEMPLATE = 'Params have not needed values "%s"';

    /** @var class-string */
    private string $class;
    private string $method;
    private int $position;

    /** @var class-string */
    private string $expected;

    /** @var class-string */
    private string $proposed;

    /**
     * @param class-string $class
     * @param class-string $expected
     * @param class-string $proposed
     */
    public function __construct(string $class, string $method, int $position, string $expected, string $proposed)
    {
        $this->class = $class;
        $this->method = $method;
        $this->position = $position;
        $this->expected = $expected;
        $this->proposed = $proposed;
    }

    public static function fromArray(array $params): self
    {
        if (!ObjectUtil::isArrayHasAllPropertiesFromClass($params, self::class)) {
            $message = sprintf(self::NOT_ALL_PARAMETERS_TEMPLATE, implode(', ', array_keys($params)));

            throw new \InvalidArgumentException($message);
        }

        return new self(
            $params['class'],
            $params['method'],
            (int) $params['position'],
            $params['expected'],
            $params['proposed']
        );
    }

    /** @return class-string */
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

    /** @return class-string */
    public function getExpected(): string
    {
        return $this->expected;
    }

    /** @return class-string */
    public function getProposed(): string
    {
        return $this->proposed;
    }
}
