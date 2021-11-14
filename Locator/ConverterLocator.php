<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Locator;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Converter\TypeConverter;
use FRZB\Component\RequestMapper\Exception\ConverterContainerException;
use FRZB\Component\RequestMapper\Exception\ConverterNotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as ServiceLocator;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedLocator;

#[AsService]
final class ConverterLocator implements ConverterLocatorInterface
{
    private ServiceLocator $serviceLocator;

    public function __construct(
        #[TaggedLocator(self::REQUEST_MAPPER_CONVERTERS_TAG, defaultIndexMethod: 'getType')]
        ServiceLocator $serviceLocator
    ) {
        $this->serviceLocator = $serviceLocator;
    }

    public function get(string $type): TypeConverter
    {
        try {
            return $this->serviceLocator->get($type);
        } catch (NotFoundExceptionInterface $e) {
            throw new ConverterNotFoundException($type, (int) $e->getCode(), $e);
        } catch (ContainerExceptionInterface $e) {
            throw new ConverterContainerException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    public function has(string $type): bool
    {
        return $this->serviceLocator->has($type);
    }
}
