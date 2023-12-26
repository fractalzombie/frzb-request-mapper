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

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

#[Group('request-mapper')]
class ConstraintHelperTest extends TestCase
{
    #[DataProvider('fromPropertyCaseProvider')]
    public function testFromPropertyMethod(array $properties, array $expectedConstraintClasses): void
    {
        foreach ($properties as $property) {
            self::assertSame(
                $expectedConstraintClasses[$property->getName()],
                array_map(static fn (Constraint $constraint) => $constraint::class, ConstraintsHelper::fromProperty($property))
            );
        }
    }

    public static function fromPropertyCaseProvider(): iterable
    {
        yield sprintf('with "%s"', CreateUserRequest::class) => [
            'properties' => (new \ReflectionClass(CreateUserRequest::class))->getProperties(),
            'expected_constraint_classes' => [
                'name' => [NotBlank::class, Type::class],
                'userId' => [Uuid::class, Type::class],
                'amount' => [Type::class],
            ],
        ];

        yield sprintf('with "%s"', TestRequest::class) => [
            'properties' => (new \ReflectionClass(TestRequest::class))->getProperties(),
            'expected_constraint_classes' => ['name' => [], 'model' => []],
        ];
    }
}
