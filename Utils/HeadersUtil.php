<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class HeadersUtil
{
    public static function getHeaders(Request $request): array
    {
        return array_map(static fn (array $value) => current($value) ?: null, $request->headers->all());
    }
}
