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

namespace FRZB\Component\RequestMapper\Tests\Func\Extractor;

use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateNestedUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserSettingsRequest;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

#[Group('request-mapper')]
/**
 * @internal
 */
class ConstraintExtractorTest extends KernelTestCase
{
    private ConstraintExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = self::getContainer()->get(ConstraintExtractor::class);
    }

    public function testExtractMethod(): void
    {
        $constraints = $this->extractor->extract(CreateUserRequest::class);

        self::assertNull($this->extractor->extract('NoClassMustBeNull'));

        self::assertSame(Required::class, $constraints?->fields['name']::class);
        self::assertSame(NotBlank::class, $constraints?->fields['name']?->constraints[0]::class);
        self::assertSame(Type::class, $constraints?->fields['name']?->constraints[1]::class);

        self::assertSame(Required::class, $constraints?->fields['userId']::class);
        self::assertSame(Uuid::class, $constraints?->fields['userId']?->constraints[0]::class);
        self::assertSame(Type::class, $constraints?->fields['userId']?->constraints[1]::class);

        self::assertSame(Required::class, $constraints?->fields['amount']::class);
        self::assertSame(Type::class, $constraints?->fields['amount']?->constraints[0]::class);

        $constraints = $this->extractor->extract(CreateNestedUserRequest::class);

        self::assertSame(Required::class, $constraints?->fields['name']::class);
        self::assertSame(NotBlank::class, $constraints?->fields['name']?->constraints[0]::class);
        self::assertSame(Type::class, $constraints?->fields['name']?->constraints[1]::class);

        self::assertSame(Required::class, $constraints?->fields['request']::class);
        self::assertSame(Required::class, $constraints?->fields['request']->constraints[0]->fields['name']::class);
        self::assertSame(NotBlank::class, $constraints?->fields['request']->constraints[0]->fields['name']->constraints[0]::class);
        self::assertSame(Type::class, $constraints?->fields['request']->constraints[0]->fields['name']->constraints[1]::class);
        self::assertSame(Uuid::class, $constraints?->fields['request']->constraints[0]->fields['userId']->constraints[0]::class);
        self::assertSame(Type::class, $constraints?->fields['request']->constraints[0]->fields['userId']->constraints[1]::class);
        self::assertSame(Type::class, $constraints?->fields['request']->constraints[0]->fields['amount']->constraints[0]::class);

        $constraints = $this->extractor->extract(CreateUserSettingsRequest::class);

        self::assertSame(Required::class, $constraints?->fields['type']::class);
        self::assertSame(Required::class, $constraints?->fields['name']::class);
    }
}
