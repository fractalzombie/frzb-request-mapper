<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorContract;
use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\FormattedError;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;

#[AsService, AsTagged(FormatterInterface::class)]
class ValidationFormatter implements FormatterInterface
{
    public function __invoke(ValidationException $e): ErrorContract
    {
        return new FormattedError(
            $e->getMessage(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::formatErrors(...$e->getErrors()),
            $e->getTrace(),
        );
    }

    public static function getExceptionClass(): string
    {
        return ValidationException::class;
    }

    public static function getPriority(): int
    {
        return 1;
    }

    private static function formatErrors(Error ...$errors): array
    {
        return ArrayList::collect($errors)
            ->map(static fn (Error $error) => [$error->getField() => $error->getMessage()])
            ->reduce(array_merge(...))
            ->getOrElse([])
        ;
    }
}
