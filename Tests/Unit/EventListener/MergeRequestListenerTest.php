<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Tests\Unit\EventListener;

use FRZB\Component\RequestMapper\EventListener\MergeRequestListener;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @group request-mapper
 *
 * @internal
 */
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
