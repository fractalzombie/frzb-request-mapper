RequestMapper Component
=============================

Installation
------------
The recommended way to install is through Composer:

```
composer require frzb/request-mapper
```

It requires PHP version 8.0 and higher.

Usage `#[ParamConverter]`
-----

ParamConverter will automatically convert your request
to `CreateUserRequest` and validate it if class properties
has constraints.

```php
<?php

use FRZB\Component\RequestMapper\Attribute\ParamConverter;
use FRZB\Component\RequestMapper\Data\ConverterType;
use FRZB\Component\RequestMapper\Tests\Stub\CreateUserRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController {
    #[Route(path: '/api/v1/users', name: self::class, methods: [Request::METHOD_POST])]
    #[ParamConverter(name: 'request', type: ConverterType::REQUEST, class: CreateUserRequest::class)]
    public function __invoke(CreateUserRequest $request): JsonResponse
    {
        $payload = [
            'name' => $request->getName(),
            'amount' => $request->getAmount(),
            'userId' => $request->getUserId(),
        ];
        
        return new JsonResponse($payload, Response::HTTP_OK);
    }
}
```
