<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Helper;

use FRZB\Component\RequestMapper\Helper\ConstraintsHelper;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\TestRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @group request-mapper
 *
 * @internal
 */
class ConstraintHelperTest extends TestCase
{
    /** @dataProvider fromPropertyCaseProvider */
    public function testFromPropertyMethod(array $properties, array $expectedConstraintClasses): void
    {
        foreach ($properties as $property) {
            self::assertSame(
                $expectedConstraintClasses[$property->getName()],
                array_map(static fn (Constraint $constraint) => $constraint::class, ConstraintsHelper::fromProperty($property))
            );
        }
    }

    public function fromPropertyCaseProvider(): iterable
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
