<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Extractor;

use FRZB\Component\RequestMapper\Extractor\ConstraintExtractor;
use FRZB\Component\RequestMapper\Extractor\ParametersExtractor;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequestWithSerializedName;
use FRZB\Component\RequestMapper\Tests\Stub\TestRequest;
use FRZB\Component\RequestMapper\Tests\Utils\TestConstant;
use PHPUnit\Framework\TestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
class ParametersExtractorTest extends TestCase
{
    private ConstraintExtractor $constraintExtractor;
    private ParametersExtractor $parametersExtractor;

    protected function setUp(): void
    {
        $this->constraintExtractor = new ConstraintExtractor();
        $this->parametersExtractor = new ParametersExtractor();
    }

    /** @dataProvider caseProvider */
    public function testExtractMethod(string $class, array $parameters): void
    {
        $constraints = $this->constraintExtractor->extract($class);
        $extractedParameters = $this->parametersExtractor->extract($class, $parameters, $constraints);

        self::assertSame($parameters, $extractedParameters);
    }

    public function caseProvider(): iterable
    {
        $parameters = [
            'name' => TestConstant::USER_NAME,
            'userId' => TestConstant::USER_ID,
            'amount' => TestConstant::USER_AMOUNT,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => $parameters,
        ];

        $parameters = [
            'name' => TestConstant::USER_NAME,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => array_merge($parameters, ['userId' => null, 'amount' => null]),
        ];

        $parameters = [
            'name' => TestConstant::UUID,
            'model' => TestConstant::USER_NAME,
        ];

        yield sprintf('"%s" with parameters: "%s"', TestRequest::class, implode(', ', array_keys($parameters))) => [
            'class' => TestRequest::class,
            'parameters' => $parameters,
        ];

        $parameters = [
            'name' => TestConstant::USER_NAME,
            'uuid' => TestConstant::USER_ID,
            'amountOfWallet' => TestConstant::USER_AMOUNT,
        ];

        yield sprintf('"%s" with parameters: "%s"', CreateUserRequestWithSerializedName::class, implode(', ', array_keys($parameters))) => [
            'class' => CreateUserRequest::class,
            'parameters' => array_merge($parameters, ['userId' => TestConstant::USER_ID, 'amount' => TestConstant::USER_AMOUNT]),
        ];
    }
}
