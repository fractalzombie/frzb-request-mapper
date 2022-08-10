<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\FormatterInterface as Formatter;
use FRZB\Component\RequestMapper\ExceptionFormatter\Formatter\ThrowableFormatter;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
class ExceptionFormatterLocator implements ExceptionFormatterLocatorInterface
{
    /** @var HashMap<string, callable|Formatter> */
    private readonly HashMap $formatters;

    public function __construct(
        #[TaggedIterator(Formatter::class, defaultIndexMethod: 'getType')] iterable $formatters,
    ) {
        $this->formatters = HashMap::collect($formatters);
    }

    public function get(\Throwable $e): Formatter|callable
    {
        return $this->formatters
            ->get($e::class)
            ->getOrElse(new ThrowableFormatter())
        ;
    }

    public function has(\Throwable $e): bool
    {
        return $this->formatters->get($e::class)->isNonEmpty();
    }
}
