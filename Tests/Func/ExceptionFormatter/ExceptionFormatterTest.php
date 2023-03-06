<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\ExceptionFormatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\FormattedError;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterInterface as ExceptionFormatter;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\NotNull;

#[Group('request-mapper')]
/**
 * @internal
 */
final class ExceptionFormatterTest extends KernelTestCase
{
    private ExceptionFormatter $formatter;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->formatter = self::getContainer()->get(ExceptionFormatter::class);
    }

    #[DataProvider('caseProvider')]
    public function test(\Throwable $e, ErrorContract $errorContract): void
    {
        $formattedErrorContract = $this->formatter->format($e);

        self::assertSame($errorContract->getMessage(), $formattedErrorContract->getMessage());
        self::assertSame($errorContract->getStatus(), $formattedErrorContract->getStatus());
        self::assertSame($errorContract->getErrors(), $formattedErrorContract->getErrors());
        self::assertIsArray($formattedErrorContract->getTrace());
        self::assertNotNull($formattedErrorContract->getTrace());
        self::assertSame((string) $errorContract, (string) $formattedErrorContract);
        self::assertJsonStringEqualsJsonString(json_encode($errorContract), json_encode($formattedErrorContract));
    }

    public static function caseProvider(): iterable
    {
        yield sprintf('Format "%s"', HttpException::class) => [
            'exception' => new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error'),
            'error_contract' => new FormattedError('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR),
        ];

        yield sprintf('Format "%s"', ValidationException::class) => [
            'exception' => ValidationException::fromErrors(new ValidationError(NotNull::class, 'field', 'field cannot be null')),
            'error_contract' => new FormattedError(ValidationException::DEFAULT_MESSAGE, Response::HTTP_UNPROCESSABLE_ENTITY, ['field' => 'field cannot be null']),
        ];

        yield sprintf('Format "%s"', \Exception::class) => [
            'exception' => new \Exception(TestConstant::EXCEPTION_MESSAGE),
            'error_contract' => new FormattedError('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR),
        ];
    }
}
