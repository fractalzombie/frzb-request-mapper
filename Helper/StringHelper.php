<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class StringHelper
{
    private function __construct()
    {
    }

    public static function toSnakeCase(string $value): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_\\0', lcfirst($value)));
    }

    public static function toKebabCase(string $value): string
    {
        return strtolower(preg_replace('/[A-Z]/', '-\\0', lcfirst($value)));
    }

    public static function toCamelCase(string $value): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value)));
    }

    public static function toLowerCamelCase(string $value): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $value))));
    }

    public static function contains(string $value, string $subValue): bool
    {
        return str_contains($value, $subValue);
    }

    public static function makePrefix(string $prefix, ?string $value = null, string $delimiter = '-'): string
    {
        return $value
            ? self::normalize($prefix).$delimiter.$value
            : self::normalize($prefix);
    }

    public static function normalize(string $value): string
    {
        return strtolower(preg_replace('/[^a-zA-Z\\d_-]/', '-', $value));
    }

    public static function removeBrackets(string $value, array $brackets = ['[', ']']): string
    {
        return str_replace($brackets, '', $value);
    }

    public static function removeNotWordCharacters(string $value): string
    {
        return preg_replace('/[\W+]+/', '', $value);
    }
}
