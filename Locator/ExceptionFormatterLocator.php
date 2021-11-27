<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Locator;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class ExceptionFormatterLocator implements ExceptionFormatterLocatorInterface
{
    /** @var array<callable|FormatterInterface> */
    private array $formatters;

    public function __construct(
        #[TaggedIterator(self::EXCEPTION_FORMATTERS_TAG, defaultIndexMethod: 'getExceptionClass', defaultPriorityMethod: 'getPriority')]
        iterable $formatters
    ) {
        $this->formatters = $formatters instanceof \Traversable ? iterator_to_array($formatters) : (array) $formatters;
    }

    public function get(\Throwable $e): FormatterInterface|callable
    {
        return $this->formatters[$e::class] ?? $this->formatters[\Throwable::class];
    }

    public function has(\Throwable $e): bool
    {
        return \array_key_exists($e::class, $this->formatters);
    }
}
