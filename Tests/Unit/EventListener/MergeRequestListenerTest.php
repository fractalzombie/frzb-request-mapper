<?php

declare(strict_types=1);

/**
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * Copyright (c) 2023 Mykhailo Shtanko fractalzombie@gmail.com
 *
 * For the full copyright and license information, please view the LICENSE.MD
 * file that was distributed with this source code.
 */

namespace FRZB\Component\RequestMapper\Tests\Unit\EventListener;

use FRZB\Component\RequestMapper\EventListener\MergeRequestListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[Group('request-mapper')]
class MergeRequestListenerTest extends TestCase
{
    private MergeRequestListener $listener;

    protected function setUp(): void
    {
        $this->listener = new MergeRequestListener();
    }

    public function testOnKernelRequestMethod(): void
    {
        $params = ['userId' => TestConstant::UUID];
        $request = RequestHelper::makeRequest(Request::METHOD_POST, $params);
        $kernel = $this->createMock(HttpKernelInterface::class);

        $this->listener->onKernelRequest(new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST));

        self::assertSame($params, $request->request->all());
    }
}
