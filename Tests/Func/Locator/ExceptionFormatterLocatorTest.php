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

namespace FRZB\Component\RequestMapper\Tests\Func\Locator;

use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\HttpExceptionFormatter;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\ThrowableFormatter;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\ValidationFormatter;
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
class ExceptionFormatterLocatorTest extends KernelTestCase
{
    private ExceptionFormatterLocator $locator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->locator = self::getContainer()->get(ExceptionFormatterLocator::class);
    }

    #[DataProvider('caseProvider')]
    public function testGetAndHasMethods(\Throwable $e, string $formatterClass, bool $existsInLocator): void
    {
        $formatter = $this->locator->get($e);

        self::assertInstanceOf($formatterClass, $formatter);
        self::assertSame($existsInLocator, $this->locator->has($e));
    }

    public static function caseProvider(): iterable
    {
        yield sprintf('"%s" uses "%s" converter', HttpException::class, HttpExceptionFormatter::class) => [
            'exception' => new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Internal Server Error'),
            'formatter_class' => HttpExceptionFormatter::class,
            'exists_in_locator' => true,
        ];

        yield sprintf('"%s" uses "%s" converter', ValidationException::class, ValidationFormatter::class) => [
            'exception' => ValidationException::fromErrors(new ValidationError(NotNull::class, 'field', 'field cannot be null')),
            'formatter_class' => ValidationFormatter::class,
            'exists_in_locator' => true,
        ];

        yield sprintf('"%s" uses "%s" converter', \Exception::class, \Throwable::class) => [
            'exception' => new \Exception(TestConstant::EXCEPTION_MESSAGE),
            'formatter_class' => ThrowableFormatter::class,
            'exists_in_locator' => false,
        ];
    }
}
