<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionMapper;

use Fp\Collections\HashMap;
use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Exception\ExceptionMapperLocatorException;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\ExceptionMapperInterface;
use FRZB\Component\RequestMapper\ExceptionMapper\Mapper\ExceptionMapperInterface as ExceptionMapper;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

#[AsService]
final class ExceptionMapperLocator implements ExceptionMapperLocatorInterface
{
    private readonly HashMap $mappers;

    public function __construct(#[TaggedIterator(ExceptionMapperInterface::class, defaultIndexMethod: 'getType')] iterable $mappers)
    {
        $this->mappers = HashMap::collect($mappers);
    }

    public function get(\Throwable $exception): ExceptionMapper
    {
        return $this->mappers
            ->get($exception::class)
            ->getOrThrow(ExceptionMapperLocatorException::notFound($exception))
        ;
    }
}
