<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Attribute;

use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/** @final */
#[Immutable]
#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
class RequestBody
{
    public const DEFAULT_VALIDATION_GROUPS = ['Default'];
    public const DEFAULT_SERIALIZER_CONTEXT = [
        AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        DenormalizerInterface::COLLECT_DENORMALIZATION_ERRORS => true,
    ];

    public readonly array $validationGroups;
    public readonly array $serializerContext;

    public function __construct(
        public readonly ?string $requestClass = null,
        public readonly ?string $argumentName = null,
        public readonly bool $isValidationNeeded = true,
        array $serializerContext = [],
        array $validationGroups = [],
        bool $useDefaultValidationGroup = true,
        bool $useDefaultSerializerContext = true,
    ) {
        $this->validationGroups = $useDefaultValidationGroup ? [...$validationGroups, ...self::DEFAULT_VALIDATION_GROUPS] : $validationGroups;
        $this->serializerContext = $useDefaultSerializerContext ? [...$serializerContext, ...self::DEFAULT_SERIALIZER_CONTEXT] : $serializerContext;
    }

    public function equals(object $object): bool
    {
        return match (true) {
            $object instanceof \ReflectionParameter => PropertyHelper::getTypeName($object) === $this->requestClass && $object->getName() === $this->argumentName,
            $object instanceof self => $object->requestClass === $this->requestClass && $object->argumentName === $this->argumentName,
            default => false
        };
    }
}
