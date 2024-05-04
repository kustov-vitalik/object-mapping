<?php

declare(strict_types=1);

namespace VKPHPUtils\Mapping\Reflection\Mixins;

use ReflectionAttribute;
use RuntimeException;
/**
 * @psalm-immutable
 */
trait AttributeAware
{
    public function hasAttribute(string $className): bool
    {
        return count($this->getAttributes($className)) > 0;
    }

    /**
     * @template    T
     * @psalm-param class-string<T> $className
     * @return      ReflectionAttribute<T>
     */
    public function getAttribute(string $className): ReflectionAttribute
    {
        foreach ($this->getAttributes($className) as $attribute) {
            return $attribute;
        }

        throw new RuntimeException(sprintf("No attribute '%s' found in `%s`", $className, $this->name));
    }
}
