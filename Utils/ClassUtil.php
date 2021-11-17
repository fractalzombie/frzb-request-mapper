<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

class ClassUtil
{
    public static function isNotBuiltinAndExists(string $class): bool
    {
        return class_exists($class) && (new \ReflectionClass($class))->getNamespaceName();
    }
}
