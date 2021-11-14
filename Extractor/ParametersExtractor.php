<?php

declare(strict_types=1);

namespace FRZB\Component\RequestMapper\Extractor;

use FRZB\Component\DependencyInjection\Attribute\AsService;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraint;

#[AsService]
final class ParametersExtractor
{
    /**
     * @param array<string, Constraint[]> $fields
     * @param array<string, mixed>        $parameters
     *
     * @return array<string, mixed>
     */
    #[Pure]
    public function extract(array $fields, array $parameters): array
    {
        $params = [];
        foreach ($parameters as $key => $value) {
            $params[$key] = $value;
        }

        $constraintKeys = array_keys($fields);
        $parameterKeys = array_keys($params);

        foreach (array_diff($constraintKeys, $parameterKeys) as $item) {
            $params[$item] = null;
        }

        return $params;
    }
}
