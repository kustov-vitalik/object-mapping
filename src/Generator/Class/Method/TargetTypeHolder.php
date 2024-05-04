<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Generator\Class\Method;

use ReflectionUnionType;
use ReflectionIntersectionType;
use ReflectionNamedType;
use Symfony\Component\PropertyInfo\Type;
use VKPHPUtils\Mapping\Exception\NotImplementedYet;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

final class TargetTypeHolder
{

    public readonly ReflectionUnionType|ReflectionIntersectionType|ReflectionNamedType $type;

    /**
     * @var Type[] 
     */
    public readonly array $typeInfo;

    public function __construct(
        ReflectionMethod|ReflectionProperty $methodOrProperty,
    ) {
        if ($methodOrProperty instanceof ReflectionMethod) {
            $this->type = $this->getType($methodOrProperty);

            $this->typeInfo = $methodOrProperty->hasTargetParameter()
                ? $methodOrProperty->getTargetParameter()->getTypeInfo()
                : $methodOrProperty->getTypeInfo();
        } else {
            $this->type = $methodOrProperty->hasType() ? $methodOrProperty->getType() : throw new NotImplementedYet();
            $this->typeInfo = $methodOrProperty->getTypeInfo();
        }

    }

    private function getType(
        ReflectionMethod $reflectionMethod
    ): ReflectionUnionType|ReflectionIntersectionType|ReflectionNamedType {
        if ($reflectionMethod->hasTargetParameter()) {
            if ($reflectionMethod->getTargetParameter()->hasType()) {
                return $reflectionMethod->getTargetParameter()->getType();
            }

            throw new NotImplementedYet(__METHOD__);
        }

        if ($reflectionMethod->hasReturnType()) {
            return $reflectionMethod->getReturnType();
        }

        throw new NotImplementedYet(__METHOD__);
    }


}
