<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Utils;

use Symfony\Component\Serializer\Annotation\SerializedName;

class SerializerUtil
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
