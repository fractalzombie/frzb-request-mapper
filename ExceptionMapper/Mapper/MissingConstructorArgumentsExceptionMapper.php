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

namespace FRZB\Component\RequestMapper\ExceptionMapper\Mapper;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorInterface;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ExceptionMapperException;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use FRZB\Component\RequestMapper\Helper\PropertyHelper;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Validator\Constraints\Type;

#[AsService, AsTagged(ExceptionMapperInterface::class)]
class MissingConstructorArgumentsExceptionMapper implements ExceptionMapperInterface
{
    private const MESSAGE_TEMPLATE = 'Class property "%s:%s" must be of type "%s", given value "%s"';
    private const REGEX = '/Cannot create an instance of "(?<class>(?:\\w+\\\\)*(?:\\w+))" from serialized data because its constructor requires parameter "(?<parameter>(\\w+))" to be present/';

    public function __invoke(MissingConstructorArgumentsException $exception, array $payload): ErrorInterface
    {
        preg_match(self::REGEX, $exception->getMessage(), $matches) ?: throw new \TypeError($exception->getMessage(), previous: $exception);
        $className = $matches['class'] ?? throw ExceptionMapperException::notMatchedGroup('class', $exception);
        $parameterName = $matches['parameter'] ?? throw ExceptionMapperException::notMatchedGroup('property', $exception);
        $parameter = ClassHelper::getMethodParameter($className, '__construct', $parameterName);
        $parameterTypeName = PropertyHelper::getTypeName($parameter);
        $classProperty = ClassHelper::getProperty($className, $parameterName);
        $propertyName = PropertyHelper::getName($classProperty);
        $propertyValue = HashMap::collect($payload)
            ->mapKV(static fn (string $key) => str_contains($key, $propertyName))
            ->toArrayList()
            ->firstElement()
            ->get()
        ;

        return new ValidationError(Type::class, "[{$parameterName}]", sprintf(self::MESSAGE_TEMPLATE, $parameterName, $className, $parameterTypeName, $propertyValue));
    }

    public static function getType(): string
    {
        return MissingConstructorArgumentsException::class;
    }
}
