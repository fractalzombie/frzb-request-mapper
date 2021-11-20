<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;

class TypeErrorInvalidArgumentException extends \InvalidArgumentException
{
    private const MESSAGE_TEMPLATE = 'Params have not needed values "%s"';

    #[Pure]
    private function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function fromParams(array $params, ?Throwable $previous = null): self
    {
        $message = sprintf(self::MESSAGE_TEMPLATE, implode(', ', array_keys($params)));

        return new self($message, $previous?->getCode() ?? 0, $previous);
    }
}
