<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class;

use RuntimeException;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionMethod;
use VKPHPUtils\Mapping\Attributes\Mapper;
use VKPHPUtils\Mapping\Generator\IValidator;
use VKPHPUtils\Mapping\Reflection\ReflectionClass;

final readonly class ClassValidator implements IValidator
{
    public function validate(): void
    {
        // TODO: Implement validate() method.
    }
}
