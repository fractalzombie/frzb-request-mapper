<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * @internal
 *
 * @codeCoverageIgnore
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import("{$this->getConfigDir()}/{packages}/*.yaml");
        $container->import("{$this->getConfigDir()}/{packages}/{$this->environment}/*.yaml");

        $container->import("{$this->getConfigDir()}/config/services.yaml");
        $container->import("{$this->getConfigDir()}/config/{services}_{$this->environment}.yaml");
    }

    private function getConfigDir(): string
    {
        return $this->getProjectDir().'/Resources';
    }
}
