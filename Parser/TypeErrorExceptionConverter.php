<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Parser;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\RequestMapper\Data\ErrorInterface as Error;
use FRZB\Component\RequestMapper\Data\TypeError;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Helper\ClassHelper;

#[AsService]
class TypeErrorExceptionConverter implements ExceptionConverterInterface
{
    public const TYPE_ERROR_MESSAGE_TEMPLATE = 'Invalid parameter "%s" type, expected "%s", proposed "%s"';
    public const ARGUMENT_ERROR_MESSAGE_TEMPLATE = 'Argument with position "%s" not exists';
    private const TYPE_ERROR_REGEX = '/Argument (?<position>\\d+) passed to (?<where>(?<class>(?:\\w+\\\\)*(?:\\w+))?(?:\\::)?(?<method>\\w+)\\(\\)) (?:must be of the type|must be an instance of|must implement interface) (?<expected>(?:\\w+\\\\)*\\w+),(?: instance of)? (?<proposed>(?:\\w+\\\\)*\\w+) given/';

    /**
     * @throws \TypeError
     * @throws \InvalidArgumentException
     */
    public function convert(\Throwable $e, array $data): Error
    {
        if (!preg_match(self::TYPE_ERROR_REGEX, $e->getMessage(), $matches)) {
            throw new \TypeError($e->getMessage(), (int) $e->getCode(), $e);
        }

        $error = TypeError::fromArray($matches);
        $parameters = ClassHelper::getMethodParameters($error->getClass(), $error->getMethod());
        $parameter = $parameters[$error->getPosition() - 1] ?? throw new \InvalidArgumentException(sprintf(self::ARGUMENT_ERROR_MESSAGE_TEMPLATE, $error->getPosition()));

        $expectedClass = ClassHelper::getShortName($error->getExpected());
        $proposedClass = ClassHelper::getShortName($error->getProposed());

        return new ValidationError(TypeError::class, $parameter->getName(), sprintf(self::TYPE_ERROR_MESSAGE_TEMPLATE, $parameter->getName(), $expectedClass, $proposedClass));
    }
}
