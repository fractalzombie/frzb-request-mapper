<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionFormatter\Formatter;

use Fp\Collections\ArrayList;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Exception\ValidationException;
use FRZB\Component\RequestMapper\ValueObject\ErrorContract;
use FRZB\Component\RequestMapper\ValueObject\ErrorInterface as Error;
use FRZB\Component\RequestMapper\ValueObject\FormattedError;
use Symfony\Component\HttpFoundation\Response;

#[AsService, AsTagged(FormatterInterface::class, priority: 0)]
class ValidationFormatter implements FormatterInterface
{
    public function __invoke(ValidationException $e): ErrorContract
    {
        return new FormattedError(
            $e->getMessage(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            self::formatErrors(...$e->getErrors()),
            $e->getTrace()
        );
    }

    public static function getType(): string
    {
        return ValidationException::class;
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
