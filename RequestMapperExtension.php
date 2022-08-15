<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RequestMapperExtension extends Extension
{
    /** @throws \Exception */
    public function load(array $configs, ContainerBuilder $container): void
    {
        (new YamlFileLoader($container, new FileLocator(__DIR__.'/Resources/config')))->load('services.yaml');
    }
}
