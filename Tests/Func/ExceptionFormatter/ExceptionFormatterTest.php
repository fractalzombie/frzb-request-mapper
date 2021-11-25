<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\ExceptionFormatter;

use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterInterface as ExceptionFormatter;
use FRZB\Component\RequestMapper\Tests\Utils\TestConstant;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @group request-mapper
 *
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

    /** @dataProvider caseProvider */
    public function test(\Throwable $e, ErrorContract $errorContract): void
    {
        $formattedErrorContract = $this->formatter->format($e);

        self::assertSame($errorContract->message, $formattedErrorContract->message);
        self::assertSame($errorContract->status, $formattedErrorContract->status);
        self::assertSame($errorContract->errors, $formattedErrorContract->errors);
        self::assertIsArray($formattedErrorContract->trace);
        self::assertNotNull($formattedErrorContract->trace);
    }

    public function caseProvider(): iterable
    {
        yield sprintf('Format "%s"', HttpException::class) => [
            'exception' => new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error'),
            'error_contract' => new ErrorContract('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR),
        ];

        yield sprintf('Format "%s"', ValidationException::class) => [
            'exception' => ValidationException::fromErrors(new ValidationError(NotNull::class, 'field', 'field cannot be null')),
            'error_contract' => new ErrorContract(ValidationException::DEFAULT_MESSAGE, Response::HTTP_UNPROCESSABLE_ENTITY, ['field' => 'field cannot be null']),
        ];

        yield sprintf('Format "%s"', \Exception::class) => [
            'exception' => new \Exception(TestConstant::EXCEPTION_MESSAGE),
            'error_contract' => new ErrorContract('Internal Server Error', Response::HTTP_INTERNAL_SERVER_ERROR),
        ];
    }
}
