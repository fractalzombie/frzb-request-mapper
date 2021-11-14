<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Tests\Stub\CreateCardSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Utils\TestConstant;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class DiscriminatorMapExtractorTest extends TestCase
{
    private DiscriminatorMapExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new DiscriminatorMapExtractor();
    }

    /** @dataProvider caseProvider */
    public function testExtractMethod(string $givenClass, string $expectedClass, array $parameters): void
    {
        $extractedClass = $this->extractor->extract($givenClass, $parameters);

        self::assertSame($expectedClass, $extractedClass);
    }

    public function caseProvider(): iterable
    {
        yield sprintf('Class "%s" without discriminator', CreateUserRequest::class) => [
            'given_class' => CreateUserRequest::class,
            'expected_class' => CreateUserRequest::class,
            'parameters' => ['name' => TestConstant::USER_NAME],
        ];

        yield sprintf('Class "%s" with discriminator type "%s" and expected class "%s"', CreateSettingsRequest::class, CreateUserSettingsRequest::TYPE, CreateUserSettingsRequest::class) => [
            'given_class' => CreateSettingsRequest::class,
            'expected_class' => CreateUserSettingsRequest::class,
            'parameters' => ['type' => CreateUserSettingsRequest::TYPE, 'name' => TestConstant::USER_NAME],
        ];

        yield sprintf('Class "%s" with discriminator type "%s" and expected class "%s"', CreateSettingsRequest::class, CreateCardSettingsRequest::TYPE, CreateCardSettingsRequest::class) => [
            'given_class' => CreateSettingsRequest::class,
            'expected_class' => CreateCardSettingsRequest::class,
            'parameters' => ['type' => CreateCardSettingsRequest::TYPE, 'primaryAccountNumber' => TestConstant::UUID],
        ];
    }
}
