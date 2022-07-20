<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Helper;

use Fp\Collections\ArrayList;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @internal
 */
final class SerializerHelper
{
    public static function getSerializedNameAttribute(\ReflectionProperty $rProperty): SerializedName
    {
        return ArrayList::collect(AttributeHelper::getAttributes($rProperty, SerializedName::class))
            ->firstElement()
            ->getOrElse(new SerializedName($rProperty->getName()))
        ;
    }
}
