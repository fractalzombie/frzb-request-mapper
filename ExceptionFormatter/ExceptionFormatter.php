<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Data\ErrorContract as ContractError;
use FRZB\Component\RequestMapper\ExceptionFormatter\ExceptionFormatterLocatorInterface as ExceptionFormatterLocator;

#[AsService]
class ExceptionFormatter implements ExceptionFormatterInterface
{
    public function __construct(
        private readonly ExceptionFormatterLocator $formatterLocator,
    ) {
    }

    public function format(\Throwable $e): ContractError
    {
        return $this->formatterLocator->get($e)($e);
    }
}
