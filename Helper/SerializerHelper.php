<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @internal
 */
final class SerializerHelper
{
    public static function getSerializedNameAttribute(\ReflectionProperty $rProperty): SerializedName
    {
        $attributes = array_map(
            static fn (\ReflectionAttribute $a) => $a->newInstance(),
            $rProperty->getAttributes(SerializedName::class),
        );

        return current($attributes) ?: new SerializedName($rProperty->getName());
    }
}
