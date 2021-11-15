<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Data;

final class ErrorContract
{
    public string $message;
    public int $status;
    public array $errors;
    public array $trace;

    public function __construct(string $message, int $status, array $errors = [], array $trace = [])
    {
        $this->message = $message;
        $this->status = $status;
        $this->errors = $errors;
        $this->trace = $trace;
    }
}
