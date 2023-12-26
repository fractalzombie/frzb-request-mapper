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

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Immutable;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\DiscriminatorMap;

#[Immutable]
final class ClassExtractorException extends \RuntimeException
{
    #[Pure]
    public function __construct(
        private readonly string $property,
        string $message,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    #[Pure]
    public static function fromDiscriminatorMapWhenParameterIsNull(DiscriminatorMap $discriminatorMap): self
    {
        $property = $discriminatorMap->getTypeProperty();
        $message = sprintf('%s cannot be null or empty', $property);

        return new self($property, $message);
    }

    #[Pure]
    public static function fromDiscriminatorMapWhenParameterInvalid(DiscriminatorMap $discriminatorMap): self
    {
        $property = $discriminatorMap->getTypeProperty();
        $expectedValues = array_keys($discriminatorMap->getMapping());
        $message = sprintf('%s can only be %s', $property, implode(', ', $expectedValues));

        return new self($message, $property);
    }

    public function getProperty(): string
    {
        return $this->property;
    }
}
