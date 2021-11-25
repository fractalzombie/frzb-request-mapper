<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

use JetBrains\PhpStorm\ArrayShape;

final class ContractError implements ContractErrorInterface
{
    public function __construct(
        private string $message,
        private int $status,
        private array $errors = [],
        private array $trace = [],
    ) {
    }

    public function __toString(): string
    {
        $errors = array_map(
            static fn (string $value, string $field) => sprintf('%s: [%s]', $field, $value),
            array_values($this->errors),
            array_keys($this->errors)
        );

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
