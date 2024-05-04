<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\DS;

use SplHeap;
use VKPHPUtils\Mapping\Reflection\ReflectionMethod;
use VKPHPUtils\Mapping\Reflection\ReflectionProperty;

/**
 * @extends SplHeap<ReflectionProperty>
 */
class TargetPropertiesHeap extends SplHeap
{
    public function __construct(
        private readonly ReflectionMethod $reflectionMethod,
        ReflectionProperty...$reflectionProperties
    ) {
        foreach ($reflectionProperties as $reflectionProperty) {
            if ($reflectionProperty->isStatic()) {
                continue;
            }

            $this->insert($reflectionProperty);
        }
    }

    protected function compare($value1, $value2): int
    {
        return $this->reflectionMethod->getMapping($value2->name)->priority
            - $this->reflectionMethod->getMapping($value1->name)->priority;
    }
}
