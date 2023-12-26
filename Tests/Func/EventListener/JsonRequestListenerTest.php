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

namespace FRZB\Component\RequestMapper\Tests\Func\EventListener;

use FRZB\Component\RequestMapper\EventListener\JsonRequestListener;
use FRZB\Component\RequestMapper\Helper\Header;
use FRZB\Component\RequestMapper\Tests\Helper\RequestHelper;
use FRZB\Component\RequestMapper\Tests\Helper\TestConstant;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface as HttpKernel;

#[Group('request-mapper')]
/**
 * @internal
 */
final class JsonRequestListenerTest extends KernelTestCase
{
    private JsonRequestListener $listener;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->listener = self::getContainer()->get(JsonRequestListener::class);
    }

    #[DataProvider('caseProvider')]
    public function testOnKernelRequestMethod(
        string $method,
        string $requestContent,
        ?string $expectedContent = null,
        array $headers = [],
        bool $throws = false
    ): void {
        $request = RequestHelper::makeRequest(method: $method, headers: $headers, content: $requestContent);

        if ($throws) {
            $this->expectException(BadRequestHttpException::class);
        }

        $this->listener->onKernelRequest(new RequestEvent(self::$kernel, $request, HttpKernel::MAIN_REQUEST));

        self::assertSame($request->get('id'), $expectedContent);
    }

    /** @throws \JsonException */
    public static function caseProvider(): iterable
    {
        yield 'with valid GET JSON request' => [
            'method' => Request::METHOD_GET,
            'request_content' => json_encode(['id' => TestConstant::UUID], \JSON_THROW_ON_ERROR),
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
        ];

        yield 'with valid POST JSON request' => [
            'method' => Request::METHOD_POST,
            'request_content' => json_encode(['id' => TestConstant::UUID], \JSON_THROW_ON_ERROR),
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
        ];

        yield 'with valid PUT JSON request' => [
            'method' => Request::METHOD_PUT,
            'request_content' => json_encode(['id' => TestConstant::UUID], \JSON_THROW_ON_ERROR),
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
        ];

        yield 'with valid PATCH JSON request' => [
            'method' => Request::METHOD_PATCH,
            'request_content' => json_encode(['id' => TestConstant::UUID], \JSON_THROW_ON_ERROR),
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
        ];

        yield 'with valid DELETE JSON request' => [
            'method' => Request::METHOD_DELETE,
            'request_content' => json_encode(['id' => TestConstant::UUID], \JSON_THROW_ON_ERROR),
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
        ];

        yield 'with invalid JSON request' => [
            'method' => Request::METHOD_POST,
            'request_content' => '{"id":}',
            'expected_content' => TestConstant::UUID,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
            'throws' => true,
        ];

        yield 'with XML request' => [
            'method' => Request::METHOD_POST,
            'request_content' => sprintf('<?xml version="1.0" encoding="UTF-8"?><root><id>%s</id></root>', TestConstant::UUID),
            'expected_content' => null,
            'headers' => [Header::CONTENT_TYPE => 'application/xml', Header::ACCEPT => 'application/xml'],
        ];

        yield 'with XML request and JSON headers' => [
            'method' => Request::METHOD_POST,
            'request_content' => sprintf('<?xml version="1.0" encoding="UTF-8"?><root><id>%s</id></root>', TestConstant::UUID),
            'expected_content' => null,
            'headers' => [Header::CONTENT_TYPE => 'application/json', Header::ACCEPT => 'application/json'],
            'throws' => true,
        ];
    }
}
