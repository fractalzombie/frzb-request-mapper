<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\Resources;

use FRZB\Component\DependencyInjection\DependencyInjectionBundle;
use FRZB\Component\RequestMapper\RequestMapperBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

/**
 * @group request-mapper
 *
 * @internal
 */
class BundlesTest extends TestCase
{
    public function testConfiguredBundles(): void
    {
        $bundles = require __DIR__.'/../../../Resources/bundles.php';
        $expectedBundles = [
            FrameworkBundle::class => ['all' => true],
            DependencyInjectionBundle::class => ['all' => true],
            RequestMapperBundle::class => ['all' => true],
        ];

        self::assertSame($expectedBundles, $bundles);
    }
}
