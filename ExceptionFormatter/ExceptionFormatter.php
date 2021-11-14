<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Locator\ExceptionFormatterLocatorInterface;

#[AsService]
final class ExceptionFormatter implements ExceptionFormatterInterface
{
    private ExceptionFormatterLocatorInterface $formatterLocator;

    public function __construct(ExceptionFormatterLocatorInterface $formatterLocator)
    {
        $this->formatterLocator = $formatterLocator;
    }

    public function format(\Throwable $e): ErrorContract
    {
        return $this->formatterLocator->get($e)->format($e);
    }
}
