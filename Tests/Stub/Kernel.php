<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Stub;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/** @internal */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private function getConfigDir(): string
    {
        return $this->getProjectDir().'/Resources';
    }
}
