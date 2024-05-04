<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use VKPHPUtils\Mapping\Exception\RuntimeException;

class MergeTypesExtractor implements ITypesExtractor
{
    /**
     * @var ITypesExtractor[] 
     */
    private readonly array $extractors;

    public function __construct(
        ITypesExtractor...$typesExtractor
    ) {
        $this->extractors = $typesExtractor;
    }

    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array
    {
        foreach ($this->extractors as $extractor) {
            try {
                return $extractor->getMethodReturnTypes($reflectionMethod);
            } catch (RuntimeException) {
            }
        }

        throw new RuntimeException('Could not extract method return types');
    }

    public function getParameterTypes(ReflectionParameter $reflectionParameter): array
    {
        foreach ($this->extractors as $extractor) {
            try {
                return $extractor->getParameterTypes($reflectionParameter);
            } catch (RuntimeException) {
            }
        }

        throw new RuntimeException('Could not extract parameter types');
    }

    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array
    {
        foreach ($this->extractors as $extractor) {
            try {
                return $extractor->getPropertyTypes($reflectionProperty);
            } catch (RuntimeException) {
            }
        }

        throw new RuntimeException('Could not extract property types');
    }
}
