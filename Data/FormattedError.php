<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Data;

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
    ) {}

    public function __toString(): string
    {
        $errors = HashMap::collect($this->errors)
            ->mapKV(static fn (string $key, string $value) => [$key => sprintf('%s: [%s]', $key, $value)])
            ->toMergedArray()
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
