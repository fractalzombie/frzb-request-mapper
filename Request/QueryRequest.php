<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Request;

use Symfony\Component\Validator\Constraints as Assert;

abstract class QueryRequest
{
    #[Assert\Positive]
    #[Assert\Range(min: 10, max: 50)]
    public int $limit = 10;

    #[Assert\PositiveOrZero]
    public int $offset = 0;

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
