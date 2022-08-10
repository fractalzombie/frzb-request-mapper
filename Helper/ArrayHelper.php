<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use JetBrains\PhpStorm\Immutable;

/** @internal */
#[Immutable]
final class ArrayHelper
{
    private function __construct()
    {
    }

    /** Flatten a multi-dimensional associative array with dots. */
    public static function flatten(iterable $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                $results = array_merge($results, self::flatten($value, $prepend.$key.'.'));
            } else {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /** Convert a flatten "dot" notation array into an expanded array. */
    public static function unFlatten(iterable $array): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            self::set($results, $key, $value);
        }

        return $results;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     * If no key is given to the method, the entire array will be replaced.
     */
    public static function set(iterable &$array, null|string|int $key, mixed $value): array
    {
        if (null === $key) {
            return $array = $value;
        }

        $keys = explode('.', $key);

        foreach ($keys as $i => $key) {
            if (1 === \count($keys)) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (!isset($array[$key]) || !\is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }
}
