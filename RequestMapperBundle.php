<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RequestMapperBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerExtension(new RequestMapperExtension());
    }
}
