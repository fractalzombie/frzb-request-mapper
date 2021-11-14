<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Func\Locator;

use FRZB\Component\RequestMapper\Converter\AttributeConverter;
use FRZB\Component\RequestMapper\Converter\QueryConverter;
use FRZB\Component\RequestMapper\Converter\RequestConverter;
use FRZB\Component\RequestMapper\Exception\ConverterNotFoundException;
use FRZB\Component\RequestMapper\Locator\ConverterLocatorInterface as ConverterLocator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @group request-mapper
 *
 * @internal
 */
final class ConverterLocatorTest extends KernelTestCase
{
    private ConverterLocator $locator;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->locator = self::getContainer()->get(ConverterLocator::class);
    }

    /** @dataProvider caseProvider */
    public function testLocatorMethods(
        string $converterType,
        string $converterClass,
        ?string $exceptionClass = null,
        ?string $exceptionMessage = null,
        bool $throws = false
    ): void {
        if ($throws) {
            $this->expectException($exceptionClass);
            $this->expectExceptionMessage($exceptionMessage);
        }

        $converter = $this->locator->get($converterType);

        self::assertTrue($this->locator->has($converterType));
        self::assertSame($converterClass, $converter::class);
        self::assertSame($converterType, $converter::getType());
    }

    public function caseProvider(): iterable
    {
        yield sprintf('Locate "%s" by "%s" type', RequestConverter::class, RequestConverter::getType()) => [
            'converter_type' => RequestConverter::getType(),
            'converter_class' => RequestConverter::class,
        ];

        yield sprintf('Locate "%s" by "%s" type', QueryConverter::class, QueryConverter::getType()) => [
            'converter_type' => QueryConverter::getType(),
            'converter_class' => QueryConverter::class,
        ];

        yield sprintf('Locate "%s" by "%s" type', AttributeConverter::class, AttributeConverter::getType()) => [
            'converter_type' => AttributeConverter::getType(),
            'converter_class' => AttributeConverter::class,
        ];

        yield sprintf('Locate throws "%s" with "%s" type', ConverterNotFoundException::class, 'INVALID') => [
            'converter_type' => 'INVALID',
            'converter_class' => 'NotExistConverter',
            'exception_class' => ConverterNotFoundException::class,
            'exception_message' => sprintf('Converter with type "%s" is not found.', 'INVALID'),
            'throws' => true,
        ];
    }
}
