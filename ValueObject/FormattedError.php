<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ValueObject;

use Fp\Collections\Entry;
use Fp\Collections\HashMap;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class FormattedError implements ErrorContract
{
    public function __construct(
        private readonly string $message,
        private readonly int $status,
        private readonly array $errors = [],
        private readonly array $trace = [],
    ) {
    }

    public function __toString(): string
    {
        $errors = HashMap::collect($this->errors)
            ->map(static fn (Entry $e) => sprintf('%s: [%s]', $e->key, $e->value))
            ->toAssocArray()
            ->getOrElse([])
        ;

        return sprintf('message: %s, status: %s, errors: %s', $this->message, $this->status, implode(', ', $errors));
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getTrace(): array
    {
        return $this->trace;
    }

    #[ArrayShape(['message' => 'string', 'status' => 'int', 'errors' => 'array'])]
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->message,
            'status' => $this->status,
            'errors' => $this->errors,
        ];
    }
}
