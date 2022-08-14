<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\ExceptionMapper\Mapper;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use FRZB\Component\DependencyInjection\Attribute\AsTagged;
use FRZB\Component\RequestMapper\Data\ErrorInterface;
use FRZB\Component\RequestMapper\Data\TypeError;
use FRZB\Component\RequestMapper\Data\ValidationError;
use FRZB\Component\RequestMapper\Helper\ClassHelper;
use Symfony\Component\Validator\Constraints\Type;

#[AsService, AsTagged(ExceptionMapperInterface::class)]
class TypeErrorExceptionMapper implements ExceptionMapperInterface
{
    public const TYPE_ERROR_MESSAGE_TEMPLATE = 'Invalid parameter "%s" type, expected "%s", proposed "%s"';
    public const ARGUMENT_ERROR_MESSAGE_TEMPLATE = 'Argument with position "%s" not exists';
    private const TYPE_ERROR_REGEX = '/((?<where>(?<class>(?:\\w+\\\\)*(?:\\w+))?(?:\\::)?(?<method>\\w+)\\(\\)): Argument #(?<position>\\d+) (?:\\((?<parameter>\\$\\w+)\\)) (?:must be of type) (?<expected>(?:\\w+\\\\)*\\w+),(?: instance of)? (?<proposed>(?:\\w+\\\\)*\\w+) given)/';

    /**
     * @throws \TypeError
     * @throws \InvalidArgumentException
     */
    public function __invoke(\TypeError $exception, array $payload): ErrorInterface
    {
        if (!preg_match(self::TYPE_ERROR_REGEX, $exception->getMessage(), $matches)) {
            throw new \TypeError($exception->getMessage(), (int) $exception->getCode(), $exception);
        }

        $error = TypeError::fromArray($matches);
        $parameters = ClassHelper::getMethodParameters($error->class, $error->method);
        $parameter = $parameters[$error->position - 1] ?? throw new \InvalidArgumentException(sprintf(self::ARGUMENT_ERROR_MESSAGE_TEMPLATE, $error->position));
        $parameterName = "[{$parameter->name}]";

        $expectedClass = ClassHelper::getShortName($error->expected);
        $proposedClass = ClassHelper::getShortName($error->proposed);

        return new ValidationError(Type::class, $parameterName, sprintf(self::TYPE_ERROR_MESSAGE_TEMPLATE, $parameterName, $expectedClass, $proposedClass));
    }

    public static function getType(): string
    {
        return \TypeError::class;
    }
}
