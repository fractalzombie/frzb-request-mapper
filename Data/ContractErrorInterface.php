<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

interface ContractErrorInterface extends \Stringable, \JsonSerializable
{
    public function getMessage(): string;

    public function getStatus(): int;

    public function getErrors(): array;

    public function getTrace(): array;
}
