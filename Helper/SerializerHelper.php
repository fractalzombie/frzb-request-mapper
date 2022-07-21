<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\SerializedName;

/** @internal */
#[Immutable]
final class SerializerHelper
{
    private function __construct()
    {
    }

    public static function getSerializedNameAttribute(\ReflectionProperty $rProperty): SerializedName
    {
        return ArrayList::collect(AttributeHelper::getAttributes($rProperty, SerializedName::class))
            ->firstElement()
            ->getOrElse(new SerializedName($rProperty->getName()))
        ;
    }
}
