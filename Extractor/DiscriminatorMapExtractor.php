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

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[AsService]
class DiscriminatorMapExtractor
{
    /** @throws ClassExtractorException */
    public function extract(string $className, array $payload): string
    {
        if ($discriminatorMap = ClassHelper::getAttribute($className, DiscriminatorMap::class)) {
            $property = $discriminatorMap->getTypeProperty();
            $mapping = $discriminatorMap->getMapping();
            $parameter = $payload[$property] ?? throw ClassExtractorException::fromDiscriminatorMapWhenParameterIsNull($discriminatorMap);
            $className = $mapping[$parameter] ?? throw ClassExtractorException::fromDiscriminatorMapWhenParameterInvalid($discriminatorMap);
        }

        return $className;
    }
}
