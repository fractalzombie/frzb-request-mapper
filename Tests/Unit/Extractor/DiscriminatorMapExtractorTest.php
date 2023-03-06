<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\RequestMapper\Exception\ClassExtractorException;
use FRZB\Component\RequestMapper\Extractor\DiscriminatorMapExtractor;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateCardSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateSettingsRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\Request\CreateUserSettingsRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('request-mapper')]
class DiscriminatorMapExtractorTest extends TestCase
{
    private DiscriminatorMapExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new DiscriminatorMapExtractor();
    }

    #[DataProvider('caseProvider')]
    public function testExtractMethod(string $givenClass, string $expectedClass, array $parameters, bool $throws = false): void
    {
        if ($throws) {
            $this->expectException(ClassExtractorException::class);
        }

        $extractedClass = $this->extractor->extract($givenClass, $parameters);

        self::assertSame($expectedClass, $extractedClass);
    }

    public static function caseProvider(): iterable
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

        yield sprintf('Class "%s" with invalid discriminator type "%s" and expected class "%s"', CreateSettingsRequest::class, 'invalid', CreateCardSettingsRequest::class) => [
            'given_class' => CreateSettingsRequest::class,
            'expected_class' => CreateCardSettingsRequest::class,
            'parameters' => ['type' => 'invalid', 'primaryAccountNumber' => TestConstant::UUID],
            'throws' => true,
        ];

        yield sprintf('Class "%s" with discriminator type "%s" and expected class "%s" and null parameter', CreateSettingsRequest::class, null, CreateCardSettingsRequest::class) => [
            'given_class' => CreateSettingsRequest::class,
            'expected_class' => CreateCardSettingsRequest::class,
            'parameters' => [],
            'throws' => true,
        ];

        yield sprintf('Class "%s" without discriminator and is not exists', 'NotExistedClass') => [
            'given_class' => 'NotExistedClass',
            'expected_class' => 'NotExistedClass',
            'parameters' => ['type' => CreateCardSettingsRequest::TYPE],
        ];
    }
}
