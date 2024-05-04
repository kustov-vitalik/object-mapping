<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Types\Extractor;

use ReflectionMethod;
use ReflectionParameter;
use ReflectionProperty;
use Symfony\Component\PropertyInfo\Type;

interface ITypesExtractor
{
    /**
     * @return Type[]
     */
    public function getMethodReturnTypes(ReflectionMethod $reflectionMethod): array;

    /**
     * @return Type[]
     */
    public function getParameterTypes(ReflectionParameter $reflectionParameter): array;

    /**
     * @return Type[]
     */
    public function getPropertyTypes(ReflectionProperty $reflectionProperty): array;
}
