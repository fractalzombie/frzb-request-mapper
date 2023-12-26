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

namespace FRZB\Component\RequestMapper\Tests\Stub\Controller;

use FRZB\Component\RequestMapper\Attribute\RequestBody;
use FRZB\Component\RequestMapper\Tests\Stub\Request\TestRequest;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @internal
 */
class TestCallableWithoutParameterNameController
{
    #[RequestBody(TestRequest::class)]
    public function __invoke(TestRequest $dto): JsonResponse
    {
        return new JsonResponse($dto);
    }
}
