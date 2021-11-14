<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Uuid;

/**
 * @group request-mapper
 *
 * @internal
 */
class ConstraintExtractorTest extends TestCase
{
    private ConstraintExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new ConstraintExtractor();
    }

    public function testExtractMethod(): void
    {
        $constraints = $this->extractor->extract(CreateUserRequest::class);

        self::assertSame(Required::class, $constraints->fields['name']::class);
        self::assertSame(NotBlank::class, $constraints->fields['name']->constraints[0]::class);
        self::assertSame(Type::class, $constraints->fields['name']->constraints[1]::class);

        self::assertSame(Required::class, $constraints->fields['userId']::class);
        self::assertSame(Uuid::class, $constraints->fields['userId']->constraints[0]::class);
        self::assertSame(Type::class, $constraints->fields['userId']->constraints[1]::class);

        self::assertSame(Required::class, $constraints->fields['amount']::class);
        self::assertSame(Type::class, $constraints->fields['amount']->constraints[0]::class);
    }
}
