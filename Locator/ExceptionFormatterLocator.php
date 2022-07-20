<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Locator;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class ExceptionFormatterLocator implements ExceptionFormatterLocatorInterface
{
    /** @var HashMap<string, callable|FormatterInterface> */
    private readonly HashMap $formatters;

    public function __construct(
        #[TaggedIterator(self::EXCEPTION_FORMATTERS_TAG, defaultIndexMethod: 'getExceptionClass', defaultPriorityMethod: 'getPriority')]
        iterable $formatters
    ) {
        $this->formatters = HashMap::collect($formatters);
    }

    public function get(\Throwable $e): FormatterInterface|callable
    {
        return $this->formatters
            ->get($e::class)
            ->getOrElse($this->formatters->get(\Throwable::class)->get())
        ;
    }

    public function has(\Throwable $e): bool
    {
        return $this->formatters->get($e::class)->isNonEmpty();
    }
}
