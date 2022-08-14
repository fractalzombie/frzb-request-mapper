<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ClassMapper;

use FRZB\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(ClassMapper::class)]
interface ClassMapperInterface
{
    public function map(string $className, mixed $value): array;
}
