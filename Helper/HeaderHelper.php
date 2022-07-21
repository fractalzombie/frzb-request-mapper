<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\HttpFoundation\Request;

/** @internal */
#[Immutable]
final class HeaderHelper
{
    private function __construct()
    {
    }

    public static function getHeaders(Request $request): array
    {
        return HashMap::collect($request->headers->all())
            ->map(static fn (Entry $e) => current($e->value) ?: null)
            ->toAssocArray()
            ->getOrElse([])
        ;
    }
}
