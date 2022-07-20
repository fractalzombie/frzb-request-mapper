<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use Symfony\Component\HttpFoundation\Request;

/**
 * @internal
 */
final class HeaderHelper
{
    public static function getHeaders(Request $request): array
    {
        return HashMap::collect($request->headers->all())
            ->map(static fn (Entry $e) => current($e->value) ?: null)
            ->toAssocArray()
            ->getOrElse([])
        ;
    }
}
